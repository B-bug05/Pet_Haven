{{-- We add 'medicalRecord' to our x-data to track the radio button selection --}}
<form action="{{ route('staff.pets.store') }}" method="POST" enctype="multipart/form-data" class="p-8" 
      x-data="{ photoPreview: null, medicalRecord: 'Healthy & Vaccinated' }">
    @csrf
    <div class="flex gap-10">
        {{-- Left Side: Fixed Square Photo Container --}}
        <div class="w-[300px] shrink-0">
            <label class="block text-xs font-black text-stone-400 uppercase tracking-widest mb-3">Pet Photo</label>
            <div class="relative group">
                <div class="w-[300px] h-[300px] rounded-2xl bg-stone-100 border-2 border-dashed border-stone-300 flex items-center justify-center overflow-hidden transition-all group-hover:border-orange-400 group-hover:bg-orange-50/50">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" class="w-full h-full object-cover shadow-inner">
                    </template>
                    <template x-if="!photoPreview">
                        <div class="text-center p-6">
                            <span class="text-4xl group-hover:scale-110 transition-transform inline-block">📸</span>
                            <p class="mt-2 text-[10px] text-stone-400 font-bold uppercase tracking-tight group-hover:text-orange-500">Click to Upload</p>
                        </div>
                    </template>
                </div>
                {{-- Accept only images to prevent bad file uploads --}}
                <input type="file" name="image" accept="image/jpeg, image/png, image/jpg, image/webp" class="absolute inset-0 opacity-0 cursor-pointer" 
                       @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result }; reader.readAsDataURL(file); }">
            </div>
            <p class="mt-3 text-[10px] text-stone-400 italic text-center">Max size: 2MB. JPG or PNG.</p>
        </div>

        {{-- Right Side: Fields --}}
        <div class="flex-1 space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full border-stone-200 rounded-xl focus:ring-orange-500 text-sm shadow-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full border-stone-200 rounded-xl focus:ring-orange-500 text-sm shadow-sm">
                        <option value="Ready for Adoption">Ready for Adoption</option>
                        <option value="Under Review">Under Review</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full border-stone-200 rounded-xl focus:ring-orange-500 text-sm shadow-sm">
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Age <span class="text-red-500">*</span></label>
                    <input type="text" name="age" required placeholder="e.g. 2 Years, 6 Months" class="w-full border-stone-200 rounded-xl focus:ring-orange-500 text-sm shadow-sm">
                </div>
            </div>

            {{-- NEW: Medical Record Radio Buttons --}}
            <div>
                <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-2">Medical Status <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition-all" :class="medicalRecord === 'Healthy & Vaccinated' ? 'border-orange-500 bg-orange-50/50 text-orange-700' : 'border-stone-200 hover:bg-stone-50'">
                        <input type="radio" name="health_radio" value="Healthy & Vaccinated" x-model="medicalRecord" class="text-orange-500 focus:ring-orange-500 border-stone-300">
                        <span class="text-xs font-bold">Healthy & Vaccinated</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition-all" :class="medicalRecord === 'Needs Vaccination/Deworming' ? 'border-orange-500 bg-orange-50/50 text-orange-700' : 'border-stone-200 hover:bg-stone-50'">
                        <input type="radio" name="health_radio" value="Needs Vaccination/Deworming" x-model="medicalRecord" class="text-orange-500 focus:ring-orange-500 border-stone-300">
                        <span class="text-xs font-bold">Needs Vax / Deworm</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition-all" :class="medicalRecord === 'Undergoing Treatment' ? 'border-orange-500 bg-orange-50/50 text-orange-700' : 'border-stone-200 hover:bg-stone-50'">
                        <input type="radio" name="health_radio" value="Undergoing Treatment" x-model="medicalRecord" class="text-orange-500 focus:ring-orange-500 border-stone-300">
                        <span class="text-xs font-bold">Under Treatment</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition-all" :class="medicalRecord === 'Other' ? 'border-orange-500 bg-orange-50/50 text-orange-700' : 'border-stone-200 hover:bg-stone-50'">
                        <input type="radio" name="health_radio" value="Other" x-model="medicalRecord" class="text-orange-500 focus:ring-orange-500 border-stone-300">
                        <span class="text-xs font-bold">Other (Specify)</span>
                    </label>
                </div>
                
                {{-- Dynamic 'Other' Input Field --}}
                <div x-show="medicalRecord === 'Other'" x-transition class="mt-2">
                    {{-- The :required binds to Alpine, so they CANNOT submit if this is empty and 'Other' is selected --}}
                    <input type="text" name="health_other" placeholder="Specify medical condition..." 
                           class="w-full border-stone-200 rounded-xl focus:ring-orange-500 text-sm shadow-sm" 
                           :required="medicalRecord === 'Other'">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Biography</label>
                <textarea name="description" rows="3" class="w-full border-stone-200 rounded-xl focus:ring-orange-500 text-sm shadow-sm" placeholder="Tell us about the pet's personality..."></textarea>
            </div>
        </div>
    </div>
    
    <div class="mt-8 pt-6 border-t border-stone-100 flex justify-end gap-3">
        <button type="button" @click="showModal = false" class="px-6 py-2.5 text-stone-500 font-bold text-sm hover:text-stone-800 transition">Cancel</button>
        <button type="submit" class="px-8 py-2.5 bg-orange-500 text-white rounded-xl font-bold shadow-lg shadow-orange-200 transition hover:bg-orange-600 hover:-translate-y-0.5 text-sm">Save Pet Entry</button>
    </div>
</form>