<form :action="modalType === 'add' ? '{{ route('staff.pets.store') }}' : `/staff/pets/${modalData.id}`" 
      method="POST" 
      enctype="multipart/form-data" 
      class="space-y-4">
    @csrf
    <template x-if="modalType === 'edit'">
        @method('PATCH')
    </template>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-bold text-stone-700 mb-1">Pet Name</label>
            <input type="text" name="name" :value="modalData ? modalData.name : ''" required
                   class="w-full border-stone-200 rounded-lg focus:ring-orange-500 focus:border-orange-500">
        </div>
        <div>
            <label class="block text-sm font-bold text-stone-700 mb-1">Species/Type</label>
            <select name="type" class="w-full border-stone-200 rounded-lg focus:ring-orange-500">
                <option value="Dog">Dog</option>
                <option value="Cat">Cat</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-bold text-stone-700 mb-1">Age (e.g. 2 Years)</label>
            <input type="text" name="age" :value="modalData ? modalData.age : ''" required
                   class="w-full border-stone-200 rounded-lg focus:ring-orange-500">
        </div>
        <div>
            <label class="block text-sm font-bold text-stone-700 mb-1">Status</label>
            <select name="status" class="w-full border-stone-200 rounded-lg focus:ring-orange-500">
                <option value="Ready for Adoption">Ready for Adoption</option>
                <option value="Under Review">Under Review</option>
                <option value="Application Pending">Application Pending</option>
                <option value="Found a Home">Found a Home</option>
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-bold text-stone-700 mb-1">Description</label>
        <textarea name="description" rows="3" class="w-full border-stone-200 rounded-lg focus:ring-orange-500" 
                  x-text="modalData ? modalData.description : ''"></textarea>
    </div>

    <div class="mt-6 flex justify-end gap-3">
        <button type="button" @click="showModal = false" class="px-4 py-2 bg-stone-100 text-stone-700 rounded-lg">Cancel</button>
        <button type="submit" class="px-5 py-2 bg-orange-500 text-white rounded-lg font-bold" 
                x-text="modalType === 'add' ? 'Create Pet' : 'Update Pet'"></button>
    </div>
</form>