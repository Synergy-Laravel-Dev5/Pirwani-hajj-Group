<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Company;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::with(['company', 'user'])
            ->latest()
            ->get();
        return view('lead.index', compact('leads'));
    }

    public function create()
    {
        $companies = Company::all();
        $packages = Package::all();
        $users = User::all();
        return view('lead.create', compact('companies', 'users', 'packages'));
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'package_id' => 'nullable|exists:packages,id',
            'number_of_pax' => 'nullable',
            'source' => 'nullable',
            'medium_of_contact' => 'nullable',
            'user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',

        ]);

        Lead::create($data);
        return redirect()
            ->route('lead.index')
            ->with('success', 'Lead created successfully');
    }

    public function show($id)
    {
        $lead = Lead::with(['company', 'package', 'user', 'followUps'])->findOrFail($id);

        return view('lead.show', compact('lead'));
    }


    public function edit(Lead $lead)
    {
        $packages = Package::all();
        $companies = Company::all();
        $users = User::all();
        return view(
            'lead.edit',
            compact(
                'lead',
                'companies',
                'users',
                'packages'
            )
        );
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'package_id' => 'nullable|exists:packages,id',
            'number_of_pax' => 'nullable',
            'source' => 'nullable',
            'medium_of_contact' => 'nullable',
            'user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);
        $lead->update($data);
        return redirect()
            ->route('lead.index')
            ->with('success', 'Lead updated successfully');
    }
    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()
            ->route('lead.index')
            ->with('success', 'Lead deleted successfully');
    }
}
