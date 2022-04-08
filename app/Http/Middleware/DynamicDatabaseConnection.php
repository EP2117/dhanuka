<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Config;
use DB;

class DynamicDatabaseConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $year = Session::get('loginYear');
        if($year == '2021') {
            Config::set('database.connections.mysql.database', 'dhanukaapp_db');
            DB::purge('mysql');
            DB::reconnect('mysql');
        } else if($year == '2020') {
            Config::set('database.connections.mysql.database', 'dhanuka_2021_June'); 
            DB::purge('mysql');
            DB::reconnect('mysql');   
        } else {

        } 
        return $next($request);
    }
}
