<?php

namespace App\Http\Controllers\Admin\Ecosystem;

use App\EcosystemButton;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEcosystemButtonFormRequest;

class AdminEcosystemManagementController extends Controller
{

    /**
     * Displays all ecosystem buttons and form to create a add a button.
     * Displays Admin/Ecosystem/AdminEcosystemButtonsComponent.vue
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.ecosystem.index', [
            'buttons' => EcosystemButton::all(),
        ]);
    }

    /**
     * Creates a new button.
     *
     * @param  App\Http\Requests\Admin\StoreEcosystemButtonFormRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(StoreEcosystemButtonFormRequest $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads', 'public');

            EcosystemButton::create([
                'value' => $request->value,
                'link' => null,
                'file_name' => $path,
            ]);

            $request->session()->flash('success_message', 'Button Created!');

            return redirect()->route('admin.ecosystem.index');
        }

        EcosystemButton::create([
            'value' => $request->value,
            'link' => $request->link,
            'file_name' => null,
        ]);

        $request->session()->flash('success_message', 'Button Created!');

        return redirect()->route('admin.ecosystem.index');
    }

    /**
     * Deletes a button.
     * Utilized by Admin/Ecosystem/AdminEcosystemButtonsComponent.vue
     *
     * @param  App\EcosystemButton $button
     * @return Illuminate\Http\Response
     */
    public function destroy(EcosystemButton $button)
    {
        $button->delete();

        return response()->json(null, 200);
    }

}
