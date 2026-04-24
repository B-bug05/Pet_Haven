<form :action="`/staff/pets/${modalData?.id}`" method="POST" enctype="multipart/form-data" class="flex h-[600px] overflow-hidden" x-data="{ tab: 'details', photoPreview: null }">
    @csrf
    @method('PATCH')

    {{-- LEFT FRAME (Profile Detail) --}}
    <div class="w-[300px] bg-stone-50 p-8 border-r border-stone-200 flex flex-col items-center shrink-0">
        
        {{-- Profile Picture with Hover Edit state --}}
        <div class="relative group w-48 h-48 rounded-2xl overflow-hidden shadow-lg border-4 border-white bg-stone-200 mb-6 shrink-0 cursor-pointer">
            <template x-if="photoPreview">
                <img :src="photoPreview" class="w-full h-full object-cover">
            </template>
            <template x-if="!photoPreview">
                {{-- FIX: Using native onerror so Laravel Blade doesn't crash --}}
                <img :src="modalData?.image_url" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/400x400?text=Missing+File'">
            </template>
            
            <div class="absolute inset-0 bg-stone-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                <span class="text-white font-bold text-xs uppercase tracking-widest">Change Photo</span>
            </div>
            {{-- FIX: Changed @change to x-on:change to be extra safe from Blade --}}
            <input type="file" name="image" accept="image/jpeg, image/png, image/jpg, image/webp" class="absolute inset-0 opacity-0 cursor-pointer" 
                   x-on:change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result }; reader.readAsDataURL(file); }">
        </div>
        
        {{-- Real-time Text Binding --}}
        <h3 class="text-xl font-black text-stone-800 leading-tight text-center" x-text="modalData?.name || 'Pet Name'"></h3>
        <p class="text-orange-600 font-bold text-[10px] uppercase tracking-widest mt-1 text-center" x-text="(modalData?.type || 'Type') + ' • ' + (modalData?.age || 'Age')"></p>
        
        <div class="mt-6 w-full p-3 bg-white rounded-xl border border-stone-100 text-[10px] font-black uppercase text-center text-stone-400">
            Status: <span class="text-emerald-500" x-text="modalData?.status || 'Unknown'"></span>
        </div>

        <button type="button" @click="showModal = false" class="mt-auto w-full py-3 bg-stone-100 text-stone-600 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-stone-200 transition">
            Cancel
        </button>
    </div>

    {{-- RIGHT FRAME (Navigation & Content) --}}
    <div class="flex-1 flex flex-col bg-white overflow-hidden">
        {{-- Inner Navbar --}}
        <div class="px-8 flex border-b border-stone-100 gap-8 shrink-0 bg-white">
            <button type="button" @click="tab = 'details'" :class="tab === 'details' ? 'border-orange-500 text-orange-600' : 'border-transparent text-stone-400'" class="py-4 border-b-2 font-bold text-[10px] uppercase tracking-widest transition">Details</button>
            <button type="button" @click="tab = 'health'" :class="tab === 'health' ? 'border-orange-500 text-orange-600' : 'border-transparent text-stone-400'" class="py-4 border-b-2 font-bold text-[10px] uppercase tracking-widest transition">Health Record</button>
        </div>

        {{-- Dynamic Content Area --}}
        <div class="p-8 flex-1 overflow-y-auto">
            
            {{-- TAB 1: DETAILS --}}
            <div x-show="tab === 'details'" class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Name</label>
                        <input type="text" name="name" x-model="modalData.name" required class="w-full border-stone-200 rounded-xl focus:ring-orange-500 text-sm shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Status</label>
                        <select name="status" x-model="modalData.status" required class="w-full border-stone-200 rounded-xl focus:ring-orange-500 text-sm shadow-sm">
                            <option value="Ready for Adoption">Ready for Adoption</option>
                            <option value="Under Review">Under Review</option>
                            <option value="Found a Home">Found a Home</option>
                            <option value="No Longer Available">No Longer Available</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Type</label>
                        <select name="type" x-model="modalData.type" required class="w-full border-stone-200 rounded-xl focus:ring-orange-500 text-sm shadow-sm">
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Age</label>
                        <input type="text" name="age" x-model="modalData.age" required class="w-full border-stone-200 rounded-xl focus:ring-orange-500 text-sm shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-2">Biography</label>
                    <textarea name="description" x-model="modalData.description" rows="4" class="w-full border-stone-200 rounded-xl text-sm focus:ring-orange-500 shadow-sm"></textarea>
                </div>
            </div>
            
            {{-- TAB 2: HEALTH RECORD --}}
            <div x-show="tab === 'health'" class="space-y-4">
                <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-2">Current Health Summary</label>
                <div class="p-4 border border-stone-200 rounded-xl bg-stone-50">
                    <p class="text-sm font-bold text-stone-800" x-text="modalData?.health_summary || 'No medical records logged.'"></p>
                </div>

                <div class="pt-4 border-t border-stone-100">
                    <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-2">Update Health Summary</label>
                    <textarea name="health_summary" x-model="modalData.health_summary" rows="3" class="w-full border-stone-200 rounded-xl focus:ring-orange-500 text-sm shadow-sm" placeholder="e.g. Healthy & Vaccinated, Needs Deworming..."></textarea>
                    <p class="text-[10px] text-stone-400 mt-1 italic">This overwrites the current medical status.</p>
                </div>
            </div>

        </div>

        {{-- Fixed Bottom Action Footer --}}
        <div class="px-8 py-5 border-t border-stone-100 bg-stone-50 flex justify-end shrink-0">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-2.5 rounded-xl font-bold text-sm shadow-md transition shadow-orange-200 hover:-translate-y-0.5">
                Update Pet Profile
            </button>
        </div>
    </div>
</form>