<?php

namespace Controllers;
use CustomExceptions\BadRequestException;
use CustomExceptions\ResourceNotFound;
use CustomExceptions\UnAuthorizedException;
use Helpers\RequestHelper;
use Helpers\ResourceHelper;
use Mixin\AuthenticateUser;
use Models\User;
use Constants\Rules;
use Illuminate\Pagination\Paginator;
class UserController extends BaseController
{
    use AuthenticateUser;

    protected $validationSchema = [
        "create"=>[
            "payload"=>[
                "name"=>[Rules::REQUIRED,Rules::STRING],
                "username"=>[
                    Rules::REQUIRED,
                    Rules::STRING,
                    Rules::UNIQUE => [
                        "model" => User::class
                    ]
                ],
                "password"=>[Rules::REQUIRED,Rules::STRING],
                "email"=>[
                    Rules::REQUIRED,
                    Rules::STRING,
                    Rules::UNIQUE=> [
                        "model" => User::class
                        ]
                ],
                "profile_img"=>[Rules::STRING]
            ]
        ],
        "update" => [
            "payload" => [
                "name" => [Rules::STRING],
                "email" => [
                    Rules::STRING,
                    Rules::UNIQUE => [
                        "model" => User::class
                    ]
                ],
                "username" => [
                    Rules::STRING,
                    Rules::UNIQUE => [
                        "model" => User::class
                    ]
                ],
                "profile_image" => [Rules::STRING],
            ]
        ]
    ];
    public function __construct()
    {
        $this->skipHandlers = ["create"];
        parent::__construct();
    }
    // GET /users
    protected function index(){

       return ResourceHelper::getPaginatedResource(User::class,["id","username","profile_img"]);

    }

    /**
     * @throws ResourceNotFound
     */
    // GET /users/{userId}
    protected function show($id){

       return ResourceHelper::findResourceOr404Exception(User::class, $id);
    }
    // POST /users
    protected function create(){
        $payload =RequestHelper::getRequestPayload();
        $payload["password"]=md5($payload["password"]);

        $user = User::create($payload);
        return [
            "id" => $user->id
        ];
    }
    // PUT /users/{userId}

    /**
     * @throws UnAuthorizedException
     * @throws BadRequestException
     * @throws ResourceNotFound
     */
    protected function update($id){

        $payload=RequestHelper::getRequestPayload();

        if(key_exists("password",$payload)){
            throw new BadRequestException("Unable to Update the password");
        }

        $user = ResourceHelper::findResourceOr404Exception(User::class, $id);

        $this->authenticatedUser->validateIsUserAuthorizedTo($user, "id");

        $user->update($payload);

        return [
            "message" => "updated."
        ];
    }
    // DELETE /users/{userId}

    /**
     * @throws ResourceNotFound
     */
    protected function delete($id){

        $user = ResourceHelper::findResourceOr404Exception(User::class, $id);
        $user->delete();
        return [
            "message"=>"deleted"
        ];
    }

   }