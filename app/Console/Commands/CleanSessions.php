<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Closure;
use Illuminate\Support\Facades\Auth;

class CleanSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
      public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // Verifica se há resquícios de outro usuário
            if ($request->session()->get('last_active_user') &&
                $request->session()->get('last_active_user') !== Auth::id()) {

                session()->flush();
                session()->regenerate();
            }

            // Atualiza o marcador de usuário
            $request->session()->put('last_active_user', Auth::id());
        }

        return $next($request);
    }
}
