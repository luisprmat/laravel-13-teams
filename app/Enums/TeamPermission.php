<?php

namespace App\Enums;

enum TeamPermission: string
{
    case UpdateTeam = 'team:update';
    case DeleteTeam = 'team:delete';

    case AddMember = 'member:add';
    case UpdateMember = 'member:update';
    case RemoveMember = 'member:remove';

    case CreateInvitation = 'invitation:create';
    case CancelInvitation = 'invitation:cancel';

    case CreateCategory = 'category:create';
    case UpdateCategory = 'category:update';
    case DeleteCategory = 'category:delete';

    case CreatePost = 'post:create';
    case UpdatePost = 'post:update';
    case DeletePost = 'post:delete';

}
