<div class="flex h-[550px]">
    {{-- Left Frame: Pet Detail (Context) --}}
    <div class="w-[300px] bg-stone-50 p-8 border-r border-stone-200 shrink-0">
        <label class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-4 block">Application For</label>
        
        <div class="w-full aspect-square rounded-2xl overflow-hidden shadow-inner border border-stone-200 mb-6 bg-stone-200">
            {{-- SMART ALPINE JS IMAGE DETECTOR: Checks if it starts with 'http' --}}
            <img :src="modalData?.pet?.image ? (modalData.pet.image.startsWith('http') ? modalData.pet.image : '/storage/' + modalData.pet.image) : 'https://placehold.co/400x400?text=No+Photo'" 
                 class="w-full h-full object-cover" 
                 onerror="this.src='https://placehold.co/400x400?text=Error'">
        </div>
        
        <h3 class="text-xl font-black text-stone-800" x-text="modalData?.pet?.name"></h3>
        <p class="text-xs text-stone-500 mt-2 leading-relaxed line-clamp-4" x-text="modalData?.pet?.description"></p>
    </div>

    {{-- Right Frame: Adopter Details & Action --}}
    <div class="flex-1 bg-white p-8 flex flex-col">
        <label class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-6 block">Applicant Profile</label>
        
        <div class="space-y-8 flex-1 overflow-y-auto pr-2">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-bold text-2xl shadow-lg shadow-orange-100" 
                     x-text="modalData?.user?.name ? modalData.user.name.charAt(0).toUpperCase() : ''"></div>
                <div>
                    <p class="text-xl font-black text-stone-800" x-text="modalData?.user?.name"></p>
                    <p class="text-sm text-stone-400 font-medium" x-text="modalData?.user?.email"></p>
                    <p class="text-sm text-stone-400 font-medium mt-1">📱 <span x-text="modalData?.contact_number"></span></p>
                    <p class="text-sm text-stone-400 font-medium">🏠 <span x-text="modalData?.adopter_address"></span></p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-stone-50 p-6 rounded-2xl border border-stone-100">
                    <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Adoption Statement</p>
                    <p class="text-stone-600 text-sm italic leading-relaxed" x-text="modalData?.adopter_message || 'No message provided.'"></p>
                </div>
            </div>
        </div>

        {{-- Fixed Bottom Action Buttons --}}
        <div class="mt-8 pt-6 border-t border-stone-100 shrink-0">
            <form method="POST" :action="`/staff/applications/${modalData?.id}`" class="flex gap-4 w-full">
                @csrf
                @method('PATCH')
                
                <button type="submit" name="action" value="decline" class="flex-1 py-4 bg-white border-2 border-red-100 text-red-500 rounded-2xl font-bold hover:bg-red-50 transition uppercase text-xs tracking-widest">
                    Reject Application
                </button>
                
                <button type="submit" name="action" value="approve" class="flex-1 py-4 bg-emerald-500 text-white rounded-2xl font-bold hover:bg-emerald-600 shadow-xl shadow-emerald-100 transition uppercase text-xs tracking-widest">
                    Approve Adoption
                </button>
            </form>
        </div>
    </div>
</div>