<?php

namespace App\Domains\Loans\Policies;

use App\Domains\Loans\Models\Loan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LoanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Domains\Loans\Models\Loan  $loan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Loan $loan)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Domains\Loans\Models\Loan  $loan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Loan $loan)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Domains\Loans\Models\Loan  $loan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Loan $loan)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Domains\Loans\Models\Loan  $loan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Loan $loan)
    {
        //
    }


    public function forceDelete(User $user, Loan $loan)
    {
      return $user->current_team_id === $loan->team_id
      ? Response::allow()
      : Response::deny(__('You do not own this loan'));
    }

    public function deletePayment(User $user, Loan $loan)
    {
        return $user->current_team_id === $loan->team_id
        ? Response::allow()
        : Response::deny(__('You do not own this loan'));
    }
}

