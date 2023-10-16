<?php

namespace App\Domains\Core\Services;

use Exception;
use App\Models\User;
use App\Domains\Core\Models\Signature;
use Illuminate\Database\Eloquent\Model;

class SignatureService
{
    public function createSignature(User $user, Model $model, array $form) {
        $signatureStored = Signature::where([
          "team_id" => $user->current_team_id,
          "entity_id" => $user->email,
          "signable_id" => $model->id,
          "signable_type" => $model::class,
        ])->first();

        if ($signatureStored) {
          throw new Exception(__("this document is already signed"));
        }

        $signature = Signature::create([
            'team_id' => $user->current_team_id,
            'user_id' => $user->id,
            'entity_id' => $user->email,
            'title' => $user->name,
            'subtitle' => $user->name,
            'signable_id' => $model->id,
            'signable_type' => $model::class,
            'signed_at' => date('y-m-d'),
            'text' => $form['text'] ?? "",
        ]);


        if ($form['file']) {
          $signature->addSignatureImage($form['file']);
        }
    }
    public function getSignatures(Model $model) {
        return Signature::where([
          "team_id" => $model->team_id,
          "signable_id" => $model->id,
          "signable_type" => $model::class,
        ])->get();
    }
}
