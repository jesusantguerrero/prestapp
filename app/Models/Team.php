<?php

namespace App\Models;

use Insane\Treasurer\Billable;
use App\Actions\Jetstream\AddTeamMember;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use App\Models\Traits\HasTeamProfilePhoto;
use Laravel\Jetstream\Team as JetstreamTeam;
use Laravel\Jetstream\Events\TeamMemberAdded;
use Laravel\Jetstream\Events\AddingTeamMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends JetstreamTeam
{
    use HasFactory;
    use HasTeamProfilePhoto;
    use Billable;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'personal_team' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'personal_team',
    ];

     /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
      'profile_photo_url',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];


    static function admin() {
      return Team::where('app_profile_name', 'admin')->first();
    }


    public function addMember(User $user, $roleName) {
      AddingTeamMember::dispatch($this, $user);

      if (!$this->ensureUserIsNotAlreadyOnTeam($this, $user->email)) {
        $this->users()->attach(
            $user, ['role' => $roleName]
        );

        TeamMemberAdded::dispatch($this, $user);
      }


      return $user;
    }


   protected function ensureUserIsNotAlreadyOnTeam($team, string $email)
   {
       return function ($validator) use ($team, $email) {
           $validator->errors()->addIf(
               $team->hasUserWithEmail($email),
               'email',
               __('This user already belongs to the team.')
           );
       };
   }
}
