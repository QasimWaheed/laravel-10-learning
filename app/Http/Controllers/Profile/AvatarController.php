<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Str;

class AvatarController extends Controller
{
    public function update(UpdateAvatarRequest $request)
    {
        if ($oldAvatar = $request->user()->avatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        auth()->user()->update(['avatar' => $path]);
        // dd(auth()->user());
        return back()->with('message', 'Avatar is updated.');
    }


    public function generate(Request $request) 
    {
        $result = OpenAI::images()->create([
        'prompt' => 'create avatar with cool style animated for muslim person named of Qasim Waheed',
        'n' => 1,
        'size' => '256x256'
    ]);

        if ($oldAvatar = $request->user()->avatar) {
            Storage::disk('public')->delete($oldAvatar);
        }
        $content = file_get_contents($result->data[0]->url);

        $fileName = Str::random(25);

        Storage::disk('public')->put("avatar/$fileName.jpg", $content);

        auth()->user()->update(['avatar' => "avatar/$fileName.jpg"]);
        // dd(auth()->user());
        return back()->with('message', 'Avatar is updated.');
    }
}
