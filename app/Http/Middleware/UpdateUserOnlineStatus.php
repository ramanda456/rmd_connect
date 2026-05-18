<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Events\UserStatusChanged;

class UpdateUserOnlineStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {

            // Ambil user langsung dari database via Model
            // bukan dari Auth::user() yang kadang tidak support update()
            $user = User::find(Auth::id());

            if ($user) {
                if (!$user->is_online) {
                    $user->is_online    = true;
                    $user->last_seen_at = now();
                    $user->save();

                    broadcast(new UserStatusChanged($user));

                } else {
                    $user->last_seen_at = now();
                    $user->save();
                }
            }
        }

        return $next($request);
    }
}