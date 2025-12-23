<?php

namespace App\Models;

use Auth;
use App\Models\Auth\User;
use App\Models\VideoProgress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Media extends Model
{
    protected $table = "media";
    protected $guarded = [];

    public function model()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute($value)
    {
        // If null or empty, return null
        $awsPath = $this->aws_url ?? $value;
        $type = $this->type;
        $url = $value;
        // If null or empty, return null
        if (!$awsPath) {
            return null;
        }

       
        if (!$awsPath || !Auth::check()) return null;

        $storage = config('filesystems.default');

        if( $storage == 'local') {
            return $this->aws_url;
        } else {
            
            if($type == 'upload') {
                return Storage::disk('s3')->temporaryUrl(
                        $awsPath,
                        now()->addMinutes(60)
                );
                //return $awsPath;
            } else {
                return Storage::disk('s3')->temporaryUrl(
                        $awsPath,
                        now()->addMinutes(60)
                );
            }
        }

        
    }

    public function getOldUrlAttribute()
    {
        // Return the original database 'url' value
        return $this->attributes['url'] ?? null;
    }

    public function getEmbedUrlAttribute()
    {
        // Prefer video_code if present
        $rawValue = $this->file_name ?? $this->url;

        if (!$rawValue) {
            return null;
        }

        // If it's a full YouTube URL, extract the ID and start time
        if (Str::startsWith($rawValue, ['http', 'www', 'youtube', 'youtu.be'])) {
            preg_match('/(?:v=|\/)([a-zA-Z0-9_-]{11})/', $rawValue, $matches);
            $videoId = $matches[1] ?? null;

            // Extract start time if any
            preg_match('/[?&]t=(\d+)/', $rawValue, $timeMatches);
            $startTime = $timeMatches[1] ?? 0;
        } else {
            // Example: "watch?v=gdX7ugJvuFw&t=5845s"
            preg_match('/v=([a-zA-Z0-9_-]{11})/', $rawValue, $matches);
            $videoId = $matches[1] ?? null;

            preg_match('/[?&]t=(\d+)/', $rawValue, $timeMatches);
            $startTime = $timeMatches[1] ?? 0;
        }

        if ($videoId) {
            return "https://www.youtube.com/embed/{$videoId}?start={$startTime}";
        }

        return null;
    }

    //Fetch Progress
    public function getProgress($user_id){
        $progress = null;
        $user = User::find($user_id);
        if($user){
            $progress = VideoProgress::where('user_id','=',$user_id)->where('media_id','=',$this->id)->first();
        }
        if($progress == null){
            $progress = new VideoProgress();
        }
        return $progress;
    }

    public function getProgressPercentage($user_id){
        $progress = $this->getProgress($user_id);
        if($progress->progress){
            $percentage = ($progress->progress / $progress->duration)* 100;
        }else{
            $percentage = 0;
        }
        return round($percentage,2);
    }

}
