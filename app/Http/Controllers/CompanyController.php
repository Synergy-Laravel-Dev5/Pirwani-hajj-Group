<?php

namespace App\Http\Controllers;

use App\Enums\CurrencyType;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::latest()->get();
        $trashcount = Company::onlyTrashed()->count();
        return view('companies.index', compact('companies', 'trashcount'));
    }
    public function create()
    {
        $company = null;
        return view('companies.create', compact('company'));
    }
    public function store(Request $request)
    {
        // $this->authorize('company_create');

        $request->validate([
            // Company Details
            'company_name' => 'nullable|string|max:255',
            'company_code' => 'nullable|string|max:255|unique:companies,company_code',
            'currency_type' => ['nullable', new Enum(CurrencyType::class)],
            'established_on' => 'nullable|date',
            'quota' => 'nullable|integer|min:0',
            'website' => 'nullable|url|max:255',
            'company_status_on_establishment' => 'nullable|string|max:255',
            'current_company_status' => 'nullable|string|max:255',

            // Files
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'company_stamp' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'letter_head_header' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'letter_head_footer' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'company_signature' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

            // Registration
            'mohra_enrollment_no' => 'nullable|string|max:255',
            'munazzam_no' => 'nullable|string|max:255',
            'cluster_enrollment_no' => 'nullable|string|max:255',
            'dts_no' => 'nullable|string|max:255',
            'dts_expiry' => 'nullable|date',
            'iata_no' => 'nullable|string|max:255',
            'iata_expiry' => 'nullable|date',
            'ntn' => 'nullable|string|max:255',

            // Director
            'director_name' => 'nullable|string|max:255',
            'director_cnic' => 'nullable|string|max:255',
            'director_cnic_expiry' => 'nullable|date',
            'director_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'director_detail' => 'nullable|string',

            // Bank
            'bank_name' => 'nullable|string|max:255',
            'account_title' => 'nullable|string|max:255',
            'branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',

            // Address
            'address' => 'nullable|array',
            'address.*.address_of' => 'nullable|string|max:255',
            'address.*.address' => 'nullable|string',
            'address.*.country' => 'nullable|string|max:255',
            'address.*.city' => 'nullable|string|max:255',
            'address.*.po_box' => 'nullable|string|max:255',
            'address.*.zip_code' => 'nullable|string|max:255',
            'address.*.is_preferred' => 'nullable|in:Yes,No',

            // Contact
            'contact' => 'nullable|array',
            'contact.*.contact_type' => 'nullable|string|max:255',
            'contact.*.contact' => 'nullable|string|max:255',
            'contact.*.is_preferred' => 'nullable|in:Yes,No',

            // Email
            'email' => 'nullable|array',
            'email.*.email_type' => 'nullable|string|max:255',
            'email.*.email' => 'nullable|email|max:255',
            'email.*.is_preferred' => 'nullable|in:Yes,No',

            // License
            'license' => 'nullable|array',
            'license.*.license_type' => 'nullable|string|max:255',
            'license.*.license_no' => 'nullable|string|max:255',
            'license.*.place_of_issue' => 'nullable|string|max:255',
            'license.*.date_of_issue' => 'nullable|date',
            'license.*.valid_upto' => 'nullable|date',
            'license.*.is_preferred' => 'nullable|in:Yes,No',
        ]);

        DB::beginTransaction();

        try {

            $data = $request->except([
                'company_logo',
                'company_stamp',
                'letter_head_header',
                'letter_head_footer',
                'company_signature',
                'director_photo',
                'address',
                'contact',
                'email',
                'license',
            ]);

            if ($request->hasFile('company_logo')) {
                $data['company_logo'] = $request->file('company_logo')
                    ->store('companies/logo', 'public');
            }

            if ($request->hasFile('company_stamp')) {
                $data['company_stamp'] = $request->file('company_stamp')
                    ->store('companies/stamp', 'public');
            }

            if ($request->hasFile('letter_head_header')) {
                $data['letter_head_header'] = $request->file('letter_head_header')
                    ->store('companies/letter_head/header', 'public');
            }

            if ($request->hasFile('letter_head_footer')) {
                $data['letter_head_footer'] = $request->file('letter_head_footer')
                    ->store('companies/letter_head/footer', 'public');
            }

            if ($request->hasFile('company_signature')) {
                $data['company_signature'] = $request->file('company_signature')
                    ->store('companies/signature', 'public');
            }

            if ($request->hasFile('director_photo')) {
                $data['director_photo'] = $request->file('director_photo')
                    ->store('companies/director', 'public');
            }

            $company = Company::create($data);

            // ADDRESS
            foreach ($request->input('address', []) as $addr) {
                if (empty($addr['address_of']) && empty($addr['address'])) {
                    continue;
                }

                $company->addresses()->create([
                    'address_of'   => $addr['address_of'] ?? '',
                    'address'      => $addr['address'] ?? '',
                    'country'      => $addr['country'] ?? '',
                    'city'         => $addr['city'] ?? '',
                    'po_box'       => $addr['po_box'] ?? null,
                    'zip_code'     => $addr['zip_code'] ?? null,
                    'is_preferred' => ($addr['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            // CONTACT
            foreach ($request->input('contact', []) as $con) {
                if (empty($con['contact_type']) && empty($con['contact'])) {
                    continue;
                }

                $company->contactNumbers()->create([
                    'contact_type' => $con['contact_type'] ?? '',
                    'contact'      => $con['contact'] ?? '',
                    'is_preferred' => ($con['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            // EMAIL
            foreach ($request->input('email', []) as $em) {
                if (empty($em['email_type']) && empty($em['email'])) {
                    continue;
                }

                $company->emails()->create([
                    'email_type'   => $em['email_type'] ?? '',
                    'email'        => $em['email'] ?? '',
                    'is_preferred' => ($em['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            // LICENSE
            foreach ($request->input('license', []) as $lic) {
                if (empty($lic['license_type']) && empty($lic['license_no'])) {
                    continue;
                }

                $company->licenses()->create([
                    'license_type'   => $lic['license_type'] ?? '',
                    'license_no'     => $lic['license_no'] ?? '',
                    'place_of_issue' => $lic['place_of_issue'] ?? '',
                    'date_of_issue'  => $lic['date_of_issue'] ?? null,
                    'valid_upto'     => $lic['valid_upto'] ?? null,
                    'is_preferred'   => ($lic['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('company.index')
                ->with('success', 'Company created successfully.');
        } catch (Exception $e) {

            DB::rollBack();

            foreach (
                [
                    'company_logo',
                    'company_stamp',
                    'letter_head_header',
                    'letter_head_footer',
                    'company_signature',
                    'director_photo'
                ] as $file
            ) {

                if (isset($data[$file]) && Storage::disk('public')->exists($data[$file])) {
                    Storage::disk('public')->delete($data[$file]);
                }
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        return view('companies.create', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $request->validate([
            // Company Details
            'company_name' => 'nullable|string|max:255',
            'company_code' => 'nullable|string|max:255|unique:companies,company_code,' . $company->id,
            'currency_type' => ['nullable', new Enum(CurrencyType::class)],
            'established_on' => 'nullable|date',
            'quota' => 'nullable|integer|min:0',
            'website' => 'nullable|url|max:255',
            'company_status_on_establishment' => 'nullable|string|max:255',
            'current_company_status' => 'nullable|string|max:255',

            // Files
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'company_stamp' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'letter_head_header' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'letter_head_footer' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'company_signature' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // Registration
            'mohra_enrollment_no' => 'nullable|string|max:255',
            'munazzam_no' => 'nullable|string|max:255',
            'cluster_enrollment_no' => 'nullable|string|max:255',
            'dts_no' => 'nullable|string|max:255',
            'dts_expiry' => 'nullable|date',
            'iata_no' => 'nullable|string|max:255',
            'iata_expiry' => 'nullable|date',
            'ntn' => 'nullable|string|max:255',

            // Director
            'director_name' => 'nullable|string|max:255',
            'director_cnic' => 'nullable|string|max:255',
            'director_cnic_expiry' => 'nullable|date',
            'director_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'director_detail' => 'nullable|string',

            // Bank
            'bank_name' => 'nullable|string|max:255',
            'account_title' => 'nullable|string|max:255',
            'branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',

            // Address
            'address' => 'nullable|array',
            'address.*.address_of' => 'nullable|string|max:255',
            'address.*.address' => 'nullable|string',
            'address.*.country' => 'nullable|string|max:255',
            'address.*.city' => 'nullable|string|max:255',
            'address.*.po_box' => 'nullable|string|max:255',
            'address.*.zip_code' => 'nullable|string|max:255',
            'address.*.is_preferred' => 'nullable|in:Yes,No',

            // Contact
            'contact' => 'nullable|array',
            'contact.*.contact_type' => 'nullable|string|max:255',
            'contact.*.contact' => 'nullable|string|max:255',
            'contact.*.is_preferred' => 'nullable|in:Yes,No',

            // Email
            'email' => 'nullable|array',
            'email.*.email_type' => 'nullable|string|max:255',
            'email.*.email' => 'nullable|email|max:255',
            'email.*.is_preferred' => 'nullable|in:Yes,No',

            // License
            'license' => 'nullable|array',
            'license.*.license_type' => 'nullable|string|max:255',
            'license.*.license_no' => 'nullable|string|max:255',
            'license.*.place_of_issue' => 'nullable|string|max:255',
            'license.*.date_of_issue' => 'nullable|date',
            'license.*.valid_upto' => 'nullable|date',
            'license.*.is_preferred' => 'nullable|in:Yes,No',
        ]);

        DB::beginTransaction();

        $newFiles = [];

        try {

            $data = $request->except([
                'company_logo',
                'company_stamp',
                'letter_head_header',
                'letter_head_footer',
                'company_signature',
                'director_photo',
                'address',
                'contact',
                'email',
                'license',
                '_token',
                '_method',
            ]);

            $fileFields = [
                'company_logo' => 'companies/logo',
                'company_stamp' => 'companies/stamp',
                'letter_head_header' => 'companies/letter_head/header',
                'letter_head_footer' => 'companies/letter_head/footer',
                'company_signature' => 'companies/signature',
                'director_photo' => 'companies/director',
            ];

            $oldFilesToDelete = [];

            foreach ($fileFields as $field => $path) {

                if ($request->hasFile($field)) {

                    $data[$field] = $request->file($field)->store($path, 'public');

                    $newFiles[] = $data[$field];

                    if ($company->$field) {
                        $oldFilesToDelete[] = $company->$field;
                    }
                }
            }

            $company->update($data);

            /*
        |--------------------------------------------------------------------------
        | Child Tables
        |--------------------------------------------------------------------------
        */

            $company->addresses()->delete();
            $company->contactNumbers()->delete();
            $company->emails()->delete();
            $company->licenses()->delete();

            // ADDRESS
            foreach ($request->input('address', []) as $addr) {

                if (empty($addr['address_of']) && empty($addr['address'])) {
                    continue;
                }

                $company->addresses()->create([
                    'address_of'   => $addr['address_of'] ?? '',
                    'address'      => $addr['address'] ?? '',
                    'country'      => $addr['country'] ?? '',
                    'city'         => $addr['city'] ?? '',
                    'po_box'       => $addr['po_box'] ?? null,
                    'zip_code'     => $addr['zip_code'] ?? null,
                    'is_preferred' => ($addr['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            // CONTACT
            foreach ($request->input('contact', []) as $con) {

                if (empty($con['contact_type']) && empty($con['contact'])) {
                    continue;
                }

                $company->contactNumbers()->create([
                    'contact_type' => $con['contact_type'] ?? '',
                    'contact'      => $con['contact'] ?? '',
                    'is_preferred' => ($con['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            // EMAIL
            foreach ($request->input('email', []) as $em) {

                if (empty($em['email_type']) && empty($em['email'])) {
                    continue;
                }

                $company->emails()->create([
                    'email_type'   => $em['email_type'] ?? '',
                    'email'        => $em['email'] ?? '',
                    'is_preferred' => ($em['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            // LICENSE
            foreach ($request->input('license', []) as $lic) {

                if (empty($lic['license_type']) && empty($lic['license_no'])) {
                    continue;
                }

                $company->licenses()->create([
                    'license_type'   => $lic['license_type'] ?? '',
                    'license_no'     => $lic['license_no'] ?? '',
                    'place_of_issue' => $lic['place_of_issue'] ?? '',
                    'date_of_issue'  => $lic['date_of_issue'] ?? null,
                    'valid_upto'     => $lic['valid_upto'] ?? null,
                    'is_preferred'   => ($lic['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            foreach ($oldFilesToDelete as $oldFile) {
                if (Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            DB::commit();

            return redirect()
                ->route('company.index')
                ->with('success', 'Company updated successfully.');
        } catch (Exception $e) {


            DB::rollBack();

            foreach ($newFiles as $file) {
                if (Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage()
                ]);
        }
    }

    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();

            return redirect()
                ->back()
                ->with('success', 'Company moved to trash successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    public function trash()
    {
        $companies = Company::onlyTrashed()->get();
        return view('companies.trash', compact('companies'));
    }

    public function restore($id)
    {
        try {
            $company = Company::onlyTrashed()->findOrFail($id);
            $company->restore();

            return redirect()
                ->route('company.index')
                ->with('success', 'Company restored successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $company = Company::with(['addresses', 'contactNumbers', 'emails', 'licenses'])
            ->findOrFail($id);

        return view('companies.show', compact('company'));
    }
}
