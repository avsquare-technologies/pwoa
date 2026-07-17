<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class FrontCertificateController extends Controller
{
    /**
     * Display a listing of the user's certificates.
     */
    public function index(): View
    {
        $certificates = Auth::user()->certificates()
            ->with('course')
            ->orderBy('issued_at', 'desc')
            ->get();

        return view('pages.certificates.index', compact('certificates'));
    }

    /**
     * Display the specified certificate.
     */
    public function show(string $number): View
    {
        $certificate = Certificate::where('certificate_number', $number)
            ->where('user_id', Auth::id())
            ->with(['course', 'user', 'quizResult'])
            ->firstOrFail();

        return view('pages.certificates.show', compact('certificate'));
    }
    public function mintNft(Request $request, $id, \App\Services\CertificateService $certificateService)
    {
        $request->validate([
            'image' => 'required|string', // Base64 image
        ]);

        $certificate = Certificate::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($certificate->nft_status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Certificate is already minted.'], 400);
        }

        try {
            $certificateService->mintNftFromImage($certificate, $request->input('image'));
            return response()->json(['success' => true, 'message' => 'NFT Minted Successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


}
