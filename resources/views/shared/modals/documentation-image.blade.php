{{-- Documentation Image Modal Component --}}
{{-- Usage: @include('shared.modals.documentation-image', ['imageUrl' => route(...), 'invoiceNumber' => '...', 'documentedAt' => ...]) --}}

<div id="documentationModal" class="fixed inset-0 bg-gray-900/80 dark:bg-black/90 backdrop-blur-md z-[100] hidden items-center justify-center p-4" onclick="closeDocumentationModal(event)">
    <div class="bg-white dark:bg-dark-card rounded-[2rem] shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden border border-gray-200 dark:border-dark-border" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-dark-border">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i data-lucide="image" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white">صورة التوثيق</h3>
                    <p class="text-xs text-gray-500 dark:text-dark-muted mt-0.5">طلب #{{ $invoiceNumber }}</p>
                </div>
            </div>
            <button type="button" onclick="closeDocumentationModal()" class="w-10 h-10 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 dark:hover:bg-dark-border rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        {{-- Image Content --}}
        <div class="p-6 overflow-auto max-h-[calc(90vh-120px)]">
            <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 border border-gray-200 dark:border-dark-border">
                <img id="documentationImage" data-src="{{ $imageUrl }}" alt="صورة التوثيق" class="w-full h-auto rounded-xl shadow-lg">
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-bg/50">
            <div class="text-xs text-gray-500 dark:text-dark-muted">
                <i data-lucide="calendar" class="w-3.5 h-3.5 inline-block ml-1"></i>
                تم التوثيق: {{ $documentedAt->format('Y-m-d h:i A') }}
            </div>
            <button type="button" onclick="closeDocumentationModal()" class="px-6 py-2.5 bg-gray-900 dark:bg-dark-card hover:bg-gray-800 dark:hover:bg-dark-bg text-white rounded-xl font-bold transition-colors shadow-sm border border-transparent dark:border-dark-border">
                إغلاق
            </button>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    function showDocumentationModal() {
        const modal = document.getElementById('documentationModal');
        const img = document.getElementById('documentationImage');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            if (img && !img.src) {
                img.src = img.dataset.src;
            }
            setTimeout(() => lucide.createIcons(), 50);
        }
    }
    
    function closeDocumentationModal(event) {
        if (!event || event.target.id === 'documentationModal') {
            const modal = document.getElementById('documentationModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDocumentationModal();
        }
    });
</script>
@endpush
@endonce
