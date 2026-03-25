{{-- resources/views/filament/pages/manage-service-section.blade.php --}}
<x-filament-panels::page>

    {{-- فورم إعدادات العنوان --}}
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
        <x-filament-panels::form.actions :actions="$this->getFormActions()" />
    </x-filament-panels::form>

    {{-- جدول الخدمات --}}
    <div class="mt-6">
        {{ $this->table }}
    </div>

</x-filament-panels::page>