<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Request $request): bool
    {
        return $request->path() == 'api/client' || Auth::user()->tokenCan('user:create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'password' => ['required', Password::min(8)->mixedCase()]
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $user = User::whereEmail($this->safe()->only('email'))->first();
                if ($user) {
                    $validator->errors()->add(
                        'email',
                        'Email is in use'
                    );
                }
            }
        ];
    }
}
