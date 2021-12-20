<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\System\Members;
use App\Models\System\Providers;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse as JsonResponseAlias;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/**
 *
 */
class SocialAuthController extends Controller
{

    private string $provider='';
    private object $providerDetail;
    private array $validProvider = ['facebook', 'github', 'google'];
    private string $msg ='';
    private string $accessToken='';
    private const API_RESPONSE = 'api';
    private const WEB_RESPONSE ='web';




    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param string $provider
     * @return JsonResponseAlias|void
     */
    public function redirectToProvider(string $provider)
    {
        $this->provider = $provider;
        if ($this->validateProvider()) {
            return Socialite::driver($provider)->stateless()->redirect();
        }
    }


    /**
     * Obtain the user information from Provider.
     * @param string $provider
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws Exception
     */
    public function handleProviderCallback(string $provider,Request $request)
    {
        $system = $this->systems;
        $pages = $this->pages;
        $this->provider = $provider;


        try {
            if ($this->validateProvider()) {
                $this->providerDetail = Socialite::driver($provider)->stateless()->user();
                if(!empty($this->providerDetail))
                {
                    return $this->resolve($request);
                }else{
                    throw new Exception('Unable to login using' . ucfirst($this->provider) . '. Please try again.');
                }
            }else{
                throw new Exception('Unable to login using' . ucfirst($this->provider) . '. Please try again.');
            }

        } catch (ClientException $e) {
            report($e);
            // Api Response
            //return response()->json(['error' => 'Invalid credentials provided.'], 422);
            // Web Response
            $error=$e->getMessage();
            // Same Site
            return view('pages.'.$system->themes->name.'.front.member.auth.login')->with(compact('system','error','pages'));
            // Client Site
            //return redirect(env('CLIENT_BASE_URL') . '?error=Unable to login using' . $provider . '. Please try again.');
        }


    }


    private function resolve(Request $request)
    {

        $this->memberResolver();

        if (Auth::guard('member')->check())
        {
            //Success login
            $this->accessToken = Auth::guard('member')->user()->createToken('authToken')->accessToken;
            $user = Auth::guard('member')->user();
            $user->setAttribute('last_login', Carbon::now())->save();
            return $this->send(true);

        } else{
            // Error Login
            $this->msg=ucfirst($this->provider) .' provider  error';
            return $this->send();
        }



//        $token = $userCreated->createToken('token-name')->plainTextToken;

        // For Api
        //return response()->json($userCreated, 200, ['Access-Token' => $token]);
        //For Web
//        return redirect(url('/dashboard'),302);
    }




    private function memberResolver()
    {

        // Check New Member Or Returning One
        $existMember = $this->memberExist();
        // dd($existMember);
        // Check Member Existence
        if(!is_null($existMember))
        {
            if(!$existMember->count())
            {
                // For New Member
                // Create New Member Record
                $member = Members::firstOrCreate([
                    'name'=> $this->providerDetail->getName(),
                    'email'=> $this->providerDetail->getEmail(),
                    'email_verified_at' => now(),
                    'password'=> null,
                    'status' => true,
                ]);
                if(!$member->hasProvider($this->provider))
                {
                    $member->providers()->updateOrCreate([
                        'provider_id'=> $this->providerDetail->getId(),
                        'provider'=> $this->provider,
                        'members_id'=> $member->id,
                        'avatar' => $this->providerDetail->getAvatar()
                    ]);
                }
                Auth::guard('member')->login($member);
            }else{
                // Returning Member
                Auth::guard('member')->login($existMember);
            }
        }else{
            Auth::guard('member')->login($existMember);
        }

    }



    /**
     * @return bool
     */
    protected function validateProvider()
    {
        return in_array($this->provider, $this->validProvider);
    }


    /**
     * @return null
     */
    private function memberExist()
    {
        $providerUser = $this->providerDetail;
        $provider = $this->provider;
        if($this->validateProvider())
        {
            // Check Social Record
            $providerMember = Providers::where('provider_id', $providerUser->getId())->first();
            return $providerMember ? $providerMember->user : null;
        }else{
            // Check Member Tables
            return Members::where('email', $providerUser->getEmail())
                ->orWhereHas('providers', function ($q) use ($providerUser, $provider)
                    {
                        $q->where('provider_id', $providerUser->getId())
                            ->where('provider', $provider);
                    })->first();

        }
    }

    /**
     * @param bool $status
     * @param string
     * @return Application|Factory|View|JsonResponseAlias
     */
    private function send(bool $status=false,string $type = self::WEB_RESPONSE)
    {
        if ($type === self::API_RESPONSE) {
            return response()->json(['status' => $status, 'error' => $this->msg,'token'=>$this->accessToken, ['Access-Token' => $this->accessToken]]);
        } else {
            $system = $this->systems;
            $pages = $this->pages;
            $error = $this->msg;
            if($status)
            {
                if(!empty($this->accessToken))
                {
                    return redirect(url('/') . '/auth/provider-callback?token=' . $this->accessToken);
                }else{
                    return redirect(route('member.dashboard.index',$this->accessToken),302);
                }

            }else{
                return view('pages.' . $system->themes->name . '.front.member.auth.login')->with(compact('system', 'error', 'pages'));
            }


        }

    }





}
