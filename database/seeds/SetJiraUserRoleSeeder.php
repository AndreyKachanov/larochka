<?php

use Illuminate\Database\Seeder;
use App\Entity\Jira\User;
use Illuminate\Support\Carbon;

//@codingStandardsIgnoreLine
class SetJiraUserRoleSeeder extends Seeder
{
    /**
     * @throws Throwable
     */
    public function run()
    {
        $line1 = [
            'fomina',
            'karnash',
            'kondratska',
            'kostina',
            'maksiuta.y',
            'rizhuk',
            'stepina',
        ];

        $line2 = [
            'a.kachanov',
            'chumak',
            'herasymchuk',
            'rezvanova',
            'sviridov',
            'urvant',
        ];

        try {
            DB::transaction(function () use ($line1, $line2) {
                //проставляем роли для l1
                if (User::whereIn('user_key', $line1)->count() > 0) {
                    User::whereIn('user_key', $line1)->update(['role_id' => 2]);
                }
                //проставляем роли для l2
                if (User::whereIn('user_key', $line2)->count() > 0) {
                    User::whereIn('user_key', $line2)->update(['role_id' => 3]);
                }
                //проставляем роли для обычных юзеров
                User::whereNull('role_id')->update(['role_id' => 1]);
            }, 5);
        } catch (Exception $e) {
            $errorMsg = sprintf(
                '[%s]. Error updating users role. %s.  Class - %s, line - %d',
                Carbon::now()->toDateTimeString(),
                $e->getMessage(),
                __CLASS__,
                __LINE__
            );
            dd($errorMsg);
        }
    }
}
