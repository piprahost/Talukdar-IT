<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyInfoController extends Controller
{
    /**
     * Show the form for editing company information.
     */
    public function edit()
    {
        $this->authorizePermission('view settings');
        $company = DB::table('company_settings')->first();
        
        // If no company settings exist, create default
        if (!$company) {
            DB::table('company_settings')->insert([
                'company_name' => 'ERP System',
                'service_center_name' => 'Service Center',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'phone' => '+880 1XXX XXXXXX',
                'email' => 'info@erpsystem.com',
                'terms_and_conditions' => "1. Items are repaired at customer's risk. We are not responsible for data loss during service.\n2. Service charges must be paid before delivery unless prior arrangements have been made.\n3. Unclaimed items after 30 days will be disposed of or sold to recover costs.\n4. Warranty is provided for parts replaced during service for 90 days.\n5. Customer is responsible for collecting the item within the specified delivery date.",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $company = DB::table('company_settings')->first();
        }
        
        return view('settings.company-info.edit', compact('company'));
    }

    /**
     * Update company information.
     */
    public function update(Request $request)
    {
        $this->authorizePermission('edit settings');
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'service_center_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'terms_and_conditions' => 'nullable|string',
        ]);

        $company = DB::table('company_settings')->first();
        
        if ($company) {
            DB::table('company_settings')
                ->where('id', $company->id)
                ->update(array_merge($validated, ['updated_at' => now()]));
        } else {
            DB::table('company_settings')->insert(
                array_merge($validated, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        return redirect()->route('company-info.edit')
            ->with('success', 'Company information updated successfully!');
    }
    
    /**
     * Get company settings (helper method for views)
     */
    public static function getCompanySettings()
    {
        return DB::table('company_settings')->first();
    }
}
