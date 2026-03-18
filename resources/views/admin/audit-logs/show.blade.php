@extends('admin.layouts.app')

@section('title', 'Audit Log Detail')
@section('header', 'Audit Log Detail')

@section('content')
<div class="space-y-6">
    {{-- Back button --}}
    <div>
        <a href="{{ route('admin.audit-logs.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Audit Logs
        </a>
    </div>

    {{-- Overview Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Event Overview</h2>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->created_at->format('M d, Y h:i:s A') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Action</dt>
                    <dd class="mt-1">
                        @php
                            $actionColors = [
                                'created' => 'bg-green-100 text-green-800',
                                'updated' => 'bg-blue-100 text-blue-800',
                                'deleted' => 'bg-red-100 text-red-800',
                                'restored' => 'bg-purple-100 text-purple-800',
                                'login' => 'bg-amber-100 text-amber-800',
                                'logout' => 'bg-gray-100 text-gray-800',
                            ];
                            $color = $actionColors[$auditLog->action] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $color }}">
                            {{ ucfirst($auditLog->action) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">User</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->user_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">User Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->user_type ? class_basename($auditLog->user_type) : 'System' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->model_label }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->description ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $auditLog->ip_address ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">User Agent</dt>
                    <dd class="mt-1 text-sm text-gray-500 truncate max-w-md" title="{{ $auditLog->user_agent }}">
                        {{ $auditLog->user_agent ? \Illuminate\Support\Str::limit($auditLog->user_agent, 80) : '—' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Changes Detail --}}
    @if($auditLog->action === 'updated' && !empty($auditLog->changed_fields))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Changes Made</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Field</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Old Value</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">New Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($auditLog->changed_fields as $field => $change)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $field }}</td>
                                <td class="px-6 py-3 text-sm text-red-600 font-mono">
                                    @if(is_null($change['old']))
                                        <span class="text-gray-400 italic">null</span>
                                    @elseif(is_array($change['old']) || is_object($change['old']))
                                        <pre class="text-xs">{{ json_encode($change['old'], JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        {{ $change['old'] }}
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-green-600 font-mono">
                                    @if(is_null($change['new']))
                                        <span class="text-gray-400 italic">null</span>
                                    @elseif(is_array($change['new']) || is_object($change['new']))
                                        <pre class="text-xs">{{ json_encode($change['new'], JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        {{ $change['new'] }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Raw Data for Created --}}
    @if($auditLog->action === 'created' && !empty($auditLog->new_values))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Created Values</h2>
            </div>
            <div class="p-6">
                <pre class="bg-gray-900 text-green-400 rounded-lg p-4 text-sm overflow-x-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    @endif

    {{-- Raw Data for Deleted --}}
    @if($auditLog->action === 'deleted' && !empty($auditLog->old_values))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Deleted Values</h2>
            </div>
            <div class="p-6">
                <pre class="bg-gray-900 text-red-400 rounded-lg p-4 text-sm overflow-x-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    @endif
</div>
@endsection
