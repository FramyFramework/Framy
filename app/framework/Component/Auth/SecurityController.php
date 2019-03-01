<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;

use app\framework\Component\Route\Klein\Request;
use app\framework\Component\Validation\ValidationTrait;

/**
 * Class SecurityController
 * @package app\custom\Http\Controller
 */
class SecurityController
{
    use ValidationTrait;

    public function login(Request $request)
    {
        $lastUsernameKey = "_username";

        // get the error if any (works with forward and redirect -- see below)
        // last username entered by the user
        $lastUsername = $request->paramsPost()->get($lastUsernameKey) ?: "";

        return view("auth/login", [
            'last_username' => $lastUsername,
            'error' => ""//$error
        ]);
    }

    public function check(Request $request)
    {
        $this->validateLogin($request);
    }

    public function logout()
    {

    }

    private function loginUsername(Request $request)
    {
        $post = $request->paramsPost();
        return $post->get("username") ?: $post->get("email");
    }

    private function validateLogin(Request $request)
    {
        $error[] = $this->validate($this->loginUsername($request), ['required'], false);
        $error[] = $this->validate($request->paramsPost()->get("password"), ['required'], false);
    }
}
