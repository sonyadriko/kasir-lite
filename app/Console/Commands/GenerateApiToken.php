<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:generate {email : User email} {name=API Token : Token name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate API token for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $tokenName = $this->argument('name');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        $token = $user->createToken($tokenName)->plainTextToken;
        
        $this->info("Token generated for {$user->name} ({$user->email}):");
        $this->line($token);
        
        return 0;
    }
}
