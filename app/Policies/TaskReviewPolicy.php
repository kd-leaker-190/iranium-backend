<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Task\Models\TaskReview;

class TaskReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_task::review');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskReview $taskReview): bool
    {
        return $user->can('view_task::review');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_task::review');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskReview $taskReview): bool
    {
        return $user->can('update_task::review');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskReview $taskReview): bool
    {
        return $user->can('delete_task::review');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_task::review');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, TaskReview $taskReview): bool
    {
        return $user->can('force_delete_task::review');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_task::review');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, TaskReview $taskReview): bool
    {
        return $user->can('restore_task::review');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_task::review');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, TaskReview $taskReview): bool
    {
        return $user->can('replicate_task::review');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_task::review');
    }
}
