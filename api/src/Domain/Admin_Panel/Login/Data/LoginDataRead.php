<?php

namespace App\Domain\Admin_Panel\Login\Data;

final class LoginDataRead
{
    
    public ?int $id = null;

    public ?string $name =  null;

    public ?string $username = null;

    public ?int $role_id = null;

    public ?string $role_name =  null;

    public ?string $role_type =  null;

    public ?string $scope_list =  null;

    public ?string $token =  null;

    public ?string $refresh_token = null;

}
