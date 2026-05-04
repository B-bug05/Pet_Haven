<div class="flex" style="min-height: 550px; max-height: 80vh;">
    {{-- Left Frame: Pet Detail --}}
    <div class="w-[300px] bg-stone-50 p-8 border-r border-stone-200 shrink-0 flex flex-col">
        <label class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-4 block">Application For</label>
        
        <div class="w-full aspect-square rounded-2xl overflow-hidden shadow-inner border border-stone-200 mb-6 bg-stone-200">
            <img :src="modalData?.pet?.image ? (modalData.pet.image.startsWith('http') ? modalData.pet.image : '/storage/' + modalData.pet.image) : 'https://placehold.co/400x400?text=No+Photo'" 
                 class="w-full h-full object-cover" 
                 onerror="this.src='https://placehold.co/400x400?text=Error'">
        </div>
        
        <h3 class="text-xl font-black text-stone-800" x-text="modalData?.pet?.name"></h3>
        <p class="text-xs text-stone-500 mt-2 leading-relaxed line-clamp-4" x-text="modalData?.pet?.description"></p>
    </div>

    {{-- Right Frame --}}
    <div class="flex-1 bg-white flex flex-col overflow-hidden">
        
        {{-- Scrollable content --}}
        <div class="flex-1 overflow-y-auto p-8 space-y-8">

            {{-- Applicant Profile --}}
            <div>
                <label class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-4 block">Applicant Profile</label>
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-bold text-2xl shadow-lg shadow-orange-100 shrink-0" 
                         x-text="modalData?.user?.name ? modalData.user.name.charAt(0).toUpperCase() : ''"></div>
                    <div>
                        <p class="text-xl font-black text-stone-800" x-text="modalData?.user?.name"></p>
                        <p class="text-sm text-stone-400 font-medium" x-text="modalData?.user?.email"></p>
                        <p class="text-sm text-stone-400 font-medium mt-1">📱 <span x-text="modalData?.contact_number"></span></p>
                        <p class="text-sm text-stone-400 font-medium">🏠 <span x-text="modalData?.adopter_address"></span></p>
                    </div>
                    {{-- Verification Badge --}}
                    <div class="mt-3 flex items-center gap-2">
                        <template x-if="modalData?.user?.verification_status === 'verified'">
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-black border border-emerald-200">✅ ID Verified</span>
                        </template>
                        <template x-if="modalData?.user?.verification_status === 'pending'">
                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-black border border-amber-200">⏳ ID Pending</span>
                        </template>
                        <template x-if="modalData?.user?.verification_status === 'rejected'">
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-black border border-red-200">❌ ID Rejected</span>
                        </template>
                        <template x-if="modalData?.user?.verification_status === 'unverified' || !modalData?.user?.verification_status">
                            <span class="px-3 py-1 bg-stone-100 text-stone-500 rounded-full text-xs font-black border border-stone-200">○ Unverified</span>
                        </template>
                    </div>

                    {{-- View ID + Verify/Reject buttons (show if pending) --}}
                    <template x-if="modalData?.user?.verification_status === 'pending' && modalData?.user?.id_document">
                        <div class="mt-3 space-y-2">
                            <a :href="'/storage/' + modalData.user.id_document" target="_blank"
                            class="inline-flex items-center gap-1 text-xs font-bold text-orange-600 hover:text-orange-800 transition">
                                🪪 View Submitted ID →
                            </a>
                            <form method="POST" :action="`/staff/users/${modalData?.user?.id}/verify`" class="flex gap-2 mt-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit" name="action" value="verify"
                                    class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg transition">
                                    ✅ Approve ID
                                </button>
                                <button type="submit" name="action" value="reject"
                                    class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition">
                                    ❌ Reject ID
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Structured Application Answers --}}
            <div class="bg-stone-50 rounded-2xl border border-stone-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-stone-100 bg-stone-100/60">
                    <p class="text-[10px] font-black text-stone-400 uppercase tracking-widest">Application Questionnaire</p>
                </div>
                <div class="divide-y divide-stone-100">
                    <template x-if="modalData?.housing_type">
                        <div class="px-5 py-3 flex justify-between items-start gap-4">
                            <span class="text-xs text-stone-400 font-semibold shrink-0">🏠 Housing</span>
                            <span class="text-xs text-stone-700 font-bold text-right" x-text="modalData?.housing_type"></span>
                        </div>
                    </template>
                    <template x-if="modalData?.landlord_allows_pets">
                        <div class="px-5 py-3 flex justify-between items-start gap-4">
                            <span class="text-xs text-stone-400 font-semibold shrink-0">📋 Landlord allows pets</span>
                            <span class="text-xs text-stone-700 font-bold text-right" x-text="modalData?.landlord_allows_pets"></span>
                        </div>
                    </template>
                    <template x-if="modalData?.has_other_pets">
                        <div class="px-5 py-3 flex justify-between items-start gap-4">
                            <span class="text-xs text-stone-400 font-semibold shrink-0">🐾 Other pets</span>
                            <span class="text-xs text-stone-700 font-bold text-right" x-text="modalData?.has_other_pets"></span>
                        </div>
                    </template>
                    <template x-if="modalData?.has_outdoor_space">
                        <div class="px-5 py-3 flex justify-between items-start gap-4">
                            <span class="text-xs text-stone-400 font-semibold shrink-0">🌿 Outdoor space</span>
                            <span class="text-xs text-stone-700 font-bold text-right" x-text="modalData?.has_outdoor_space"></span>
                        </div>
                    </template>
                    <template x-if="modalData?.hours_alone">
                        <div class="px-5 py-3 flex justify-between items-start gap-4">
                            <span class="text-xs text-stone-400 font-semibold shrink-0">⏰ Hours alone daily</span>
                            <span class="text-xs text-stone-700 font-bold text-right" x-text="modalData?.hours_alone"></span>
                        </div>
                    </template>
                    <template x-if="modalData?.previous_pet_experience">
                        <div class="px-5 py-3 flex justify-between items-start gap-4">
                            <span class="text-xs text-stone-400 font-semibold shrink-0">📚 Experience</span>
                            <span class="text-xs text-stone-700 font-bold text-right" x-text="modalData?.previous_pet_experience"></span>
                        </div>
                    </template>
                    <template x-if="!modalData?.housing_type && !modalData?.has_other_pets">
                        <div class="px-5 py-4 text-center">
                            <p class="text-xs text-stone-400 italic">No questionnaire data — submitted before structured form was added.</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Why this pet --}}
            <template x-if="modalData?.why_this_pet">
                <div class="bg-orange-50 p-5 rounded-2xl border border-orange-100">
                    <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Why This Pet?</p>
                    <p class="text-stone-700 text-sm leading-relaxed" x-text="modalData?.why_this_pet"></p>
                </div>
            </template>

            {{-- Adoption Statement --}}
            <template x-if="modalData?.adopter_message">
                <div class="bg-stone-50 p-5 rounded-2xl border border-stone-100">
                    <p class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-2">Additional Notes</p>
                    <p class="text-stone-600 text-sm italic leading-relaxed" x-text="modalData?.adopter_message"></p>
                </div>
            </template>

            {{-- Welfare Check-ins Section (only show for approved applications) --}}
            <template x-if="modalData?.status === 'Approved for Adoption'">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <label class="text-[10px] font-black text-stone-400 uppercase tracking-widest">Welfare Check-ins</label>
                        <form method="POST" :action="`/staff/applications/${modalData?.id}/welfare-request`">
                            @csrf
                            <button type="submit"
                                class="text-xs font-bold px-4 py-2 bg-orange-50 text-orange-600 border border-orange-200 rounded-lg hover:bg-orange-100 transition">
                                + Request Check-in
                            </button>
                        </form>
                    </div>

                    <div class="space-y-3">
                        <template x-if="modalData?.welfare_checkins?.length === 0 || !modalData?.welfare_checkins">
                            <div class="bg-stone-50 border border-stone-100 rounded-2xl p-5 text-center">
                                <p class="text-stone-400 text-sm">No check-ins yet. Request one above.</p>
                            </div>
                        </template>

                        <template x-for="checkin in (modalData?.welfare_checkins || [])" :key="checkin.id">
                            <div class="bg-stone-50 border border-stone-100 rounded-2xl p-5">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-widest"
                                        :class="checkin.status === 'submitted' ? 'text-emerald-600' : 'text-amber-500'"
                                        x-text="checkin.status === 'submitted' ? '✅ Submitted' : '⏳ Pending'">
                                    </span>
                                    <span class="text-[10px] text-stone-400" x-text="checkin.created_at"></span>
                                </div>
                                <p class="text-sm text-stone-600 leading-relaxed"
                                   x-text="checkin.message || 'Awaiting adopter response.'"></p>
                                <template x-if="checkin.photo">
                                    <img :src="'/storage/' + checkin.photo"
                                         class="mt-3 rounded-xl w-full max-h-48 object-cover border border-stone-200">
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

        </div>

        {{-- Fixed Bottom Action Buttons --}}
        <div class="px-8 py-6 border-t border-stone-100 shrink-0">
            <template x-if="modalData?.status === 'Under Review'">
                <form method="POST" :action="`/staff/applications/${modalData?.id}`" class="flex gap-4 w-full">
                    @csrf
                    @method('PATCH')
                    <button type="submit" name="action" value="decline"
                        class="flex-1 py-4 bg-white border-2 border-red-100 text-red-500 rounded-2xl font-bold hover:bg-red-50 transition uppercase text-xs tracking-widest">
                        Reject Application
                    </button>
                    <button type="submit" name="action" value="approve"
                        class="flex-1 py-4 bg-emerald-500 text-white rounded-2xl font-bold hover:bg-emerald-600 shadow-xl shadow-emerald-100 transition uppercase text-xs tracking-widest">
                        Approve Adoption
                    </button>
                </form>
            </template>
            <template x-if="modalData?.status !== 'Under Review'">
                <p class="text-center text-sm text-stone-400 italic">
                    This application has been <span class="font-bold text-stone-600" x-text="modalData?.status"></span>.
                </p>
            </template>
        </div>
    </div>
</div>