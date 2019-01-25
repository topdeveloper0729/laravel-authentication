<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use PrepareValidator;

    private $loginUserUrl = '/api/users/login';


    public function setUp()
    {
        parent::setUp();
        $this->rules = (new LoginRequest())->rules();
        $this->validator = $this->app['validator'];
    }

    public function testIdentityRule()
    {
        $this->assertTrue(
            $this->validateField('user.identity', 'carlogambino111')
        );
        $this->assertFalse(
            $this->validateField('user.identity', '')
        );
        $this->assertFalse(
            $this->validateField('user.identity', 'ut')
        );
        $this->assertFalse(
            $this->validateField('user.identity', Str::random(255))
        );
    }

    public function testPasswordRule()
    {
        $this->assertTrue(
            $this->validateField('user.password', 'mobbosspifpaf111')
        );
        $this->assertFalse(
            $this->validateField('user.password', 'mobmob')
        );
        $this->assertFalse(
            $this->validateField('user.password', Str::random(37))
        );
    }

    public function testInvalidIdentity()
    {
        $invalidIdentities = ['ff', Str::random(255)];

        foreach ($invalidIdentities as $identity) {
            $data = [
                'user' => [
                    'identity' => $identity,
                    'password' => 'sammygravano111rat'
                ]
            ];
            $this->json('POST', $this->loginUserUrl, $data)
                ->assertStatus(422)
                ->assertJsonStructure([
                    'message',
                    'errors' => [
                        'user.identity'
                    ]
                ]);
        }
    }

    public function testInvalidPassword()
    {
        $invalidPasswords = [
            '', 'qwerty', Str::random(37)
        ];

        foreach ($invalidPasswords as $password) {
            $data = [
                'user' => [
                    'identity' => 'gambinoboss',
                    'password' => $password
                ]
            ];
            $this->json('POST', $this->loginUserUrl, $data)
                ->assertStatus(422)
                ->assertJsonStructure([
                    'message',
                    'errors' => [
                        'user.password'
                    ]
                ]);
        }
    }
}
