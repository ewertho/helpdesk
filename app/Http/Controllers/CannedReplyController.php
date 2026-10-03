<?php

namespace App\Http\Controllers;

use App\Models\CannedReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CannedReplyController extends Controller
{
    public function index(): View
    {
        return view('canned.index', ['replies' => CannedReply::orderBy('title')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        CannedReply::create($request->validate([
            'title' => ['required', 'string', 'max:80'],
            'body' => ['required', 'string', 'max:5000'],
        ]));

        return back()->with('status', 'Resposta pronta salva.');
    }

    public function destroy(CannedReply $reply): RedirectResponse
    {
        $reply->delete();

        return back()->with('status', 'Resposta pronta removida.');
    }
}
