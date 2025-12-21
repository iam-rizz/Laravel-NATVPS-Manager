<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates.
     */
    public function index()
    {
        $templates = EmailTemplate::orderBy('name')->get();
        
        return view('admin.settings.email-templates.index', compact('templates'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.settings.email-templates.edit', [
            'template' => $emailTemplate,
        ]);
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $emailTemplate->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'body' => $request->body,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('settings.email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    /**
     * Preview the email template.
     */
    public function preview(EmailTemplate $emailTemplate)
    {
        // Sample data for preview
        $sampleData = [
            'user_name' => 'John Doe',
            'vps_hostname' => 'vps-demo-001',
            'vps_ip' => '192.168.1.100',
            'vps_os' => 'Ubuntu 22.04',
            'vps_ram' => '2 GB',
            'vps_cpu' => '2',
            'vps_disk' => '20 GB',
            'ssh_port' => '22001',
            'nat_ports' => '10000 - 10100',
            'user_email' => 'john@example.com',
            'login_url' => route('login'),
        ];

        $rendered = $emailTemplate->render($sampleData);

        return response($rendered['body']);
    }
}
