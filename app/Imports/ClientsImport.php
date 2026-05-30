<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class ClientsImport implements OnEachRow, WithHeadingRow, WithValidation
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();
        
        // Find existing client by email or create new one
        $client = null;
        if (!empty($data['email'])) {
            $client = Client::where('email', $data['email'])->first();
        }

        if (!$client) {
            $client = new Client();
        }

        $client->fill([
            'name'                => $data['client_name'] ?? $data['name'] ?? $client->name,
            'email'               => $data['email'] ?? $client->email,
            'mobile_no'           => $data['mobile_no'] ?? $data['phone'] ?? $client->mobile_no,
            'website'             => $data['website'] ?? $client->website,
            'project_link'        => $data['project_link'] ?? $client->project_link,
            'location'            => $data['location'] ?? $client->location,
            'technology'          => $data['technology'] ?? $client->technology,
            'linkedin'            => $data['linkedin'] ?? $client->linkedin,
            'facebook'            => $data['facebook'] ?? $client->facebook,
            'instagram'           => $data['insta'] ?? $data['instagram'] ?? $client->instagram,
            'youtube'             => $data['youtube'] ?? $client->youtube,
            'x'                   => $data['x_twitter'] ?? $data['x'] ?? $data['twitter'] ?? $client->x,
            'telegram'            => $data['telegram'] ?? $client->telegram,
            'whatsapp'            => $data['whatsapp'] ?? $client->whatsapp,
            'teams'               => $data['teams'] ?? $client->teams,
            'date_added'          => $this->transformDate($data['date'] ?? null) ?? $client->date_added ?? now(),
            'status'              => $data['status'] ?? $client->status ?? 'Lead',
            'last_contacted_date' => $this->transformDate($data['last_contacted_date'] ?? null) ?? $client->last_contacted_date,
            'follow_up_days'      => (int) ($data['followup_days'] ?? $data['follow_up_days'] ?? $client->follow_up_days ?? 7),
            'next_followup_date'  => $this->transformDate($data['next_followup_date'] ?? null) ?? $client->next_followup_date,
            'source_url'          => $data['source_url'] ?? $client->source_url,
            'assigned_to'         => auth()->id() ?? $client->assigned_to,
        ]);

        $client->save();
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function transformDate($value)
    {
        if (!$value) return null;

        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            } catch (\Throwable $e) {
            }
        }

        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
