<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logo;

class LogoApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getLogo()
    {
        try{
            $logo = Logo::first();
            return response()->json(['logo' => $logo], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editLogo($id)
    {
        try{
            $logo = Logo::find($id);
            return response()->json(['logo' => $logo], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateLogo(Request $request)
{
    try {
        $filename = Logo::find($request->id);
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $logoName = time() . '-logo.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images/logo/'), $logoName);
            $path = public_path('assets/images/logo/' . $filename->logo);
            if (!empty($filename->logo)) {
                unlink($path);
            }

            $logoFullPath = public_path('assets/images/logo/') . $logoName;
        } else {
            $logoName = $filename->logo;
            $logoFullPath = public_path('assets/images/logo/') . $logoName;
        }

        Logo::where('id', $request->id)->update([
            'logo' => $logoFullPath,
        ]);

        return response()->json([
            'status' => 200,
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


}