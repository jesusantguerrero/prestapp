<?php

namespace App\Domains\Core\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Signature extends Model
{
    use HasFactory;
    protected $fillable = [
      'team_id',
      'user_id',
      'client_id',
      'entity_id',
      'signable_id',
      'signable_type',
      'title',
      'subtitle',
      'firm',
      'signed_at'
    ];

    public function addSignatureImage(UploadedFile $photo)
    {
      $previousImage = $this->image_url;
      $this->forceFill([
          'image_url' => $photo->storePublicly(
              'signature-photos', ['disk' => $this->signaturePhotoDisk()]
          ),
      ])->save();

      if ($this->image_url) {
          Storage::disk($this->signaturePhotoDisk())->delete($previousImage);
      }
    }


    protected function signaturePhotoDisk()
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : config('atmosphere.signature_photo_disk', 'public');
    }
}
