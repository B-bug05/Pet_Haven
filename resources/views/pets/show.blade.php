@extends('layouts.app')

@section('title', $pet->name . ' | Pet Profile')

@section('content')

@php
    // The bulletproof image formatter
    $imageUrl = 'https://images.unsplash.com/photo-1543852786-1cf6624b9987?q=80&w=400&auto=format&fit=crop';
    if (!empty($pet->image)) {
        $imageUrl = \Illuminate\Support\Str::startsWith($pet->image, ['http://', 'https://']) ? $pet->image : asset('storage/' . $pet->image);
    } elseif (!empty($pet->image_url)) {
        $imageUrl = $pet->image_url;
    }

    // 🧠 THE BRAIN: Check if the logged-in user has an application for this specific pet
    $userApplication = null;
    if (auth()->check()) {
        $userApplication = auth()->user()->applications()->where('pet_id', $pet->id)->first();
    }
@endphp

<style>
    /* Tab Styles */
    .pet-tab { background: none; border: none; font-size: 1.1rem; font-weight: 600; color: #999; padding: 0.5rem 1rem; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s; margin-right: 1rem; }
    .pet-tab:hover { color: #333; }
    .pet-tab.active { color: #e67e22; border-bottom-color: #e67e22; }
    .tab-panel { display: none; animation: fadeIn 0.3s ease; }
    .tab-panel.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    /* Modal Backdrop Blur */
    dialog::backdrop {
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
    }
</style>

<div style="max-width: 1100px; margin: 3rem auto; padding: 0 1.5rem;">
    
    <a href="{{ route('discover') }}" style="display: inline-block; margin-bottom: 1.5rem; color: #999; text-decoration: none; font-weight: 500;">
        &larr; Back to Discover
    </a>

    <div style="display: flex; flex-wrap: wrap; background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.08); min-height: 600px; border: 1px solid #f0ebe1;">
        
        <div style="flex: 1; min-width: 300px; max-width: 400px; background: #fdfaf6; padding: 3rem 2rem; border-right: 1px solid #f0ebe1; display: flex; flex-direction: column; align-items: center; text-align: center;">
            
            <img src="{{ $imageUrl }}" alt="{{ $pet->name }}" 
                 style="width: 220px; height: 220px; border-radius: 50%; object-fit: cover; margin-bottom: 1.5rem; border: 6px solid white; box-shadow: 0 8px 16px rgba(0,0,0,0.1);"
                 onerror="this.src='https://placehold.co/400x400'">
            
            <h1 style="margin: 0 0 0.5rem 0; font-size: 2.5rem; color: #333;">{{ $pet->name }}</h1>
            
            <span style="padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; margin-bottom: 1.5rem; display: inline-block;
                @if($pet->status === 'Ready for Adoption')
                    background: #D4EDDA; color: #155724;
                @elseif($pet->status === 'Under Review')
                    background: #E0E7FF; color: #3730A3;
                @elseif($pet->status === 'Found a Home')
                    background: #FFF3CD; color: #856404;
                @else
                    background: #F8D7DA; color: #721C24;
                @endif">
                {{ $pet->status }}
            </span>

            <div style="width: 100%; text-align: left; background: white; padding: 1.5rem; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                <h4 style="margin: 0 0 1rem 0; color: #666; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Quick Summary</h4>
                <p style="margin: 0 0 0.5rem 0; color: #333;"><strong>Type:</strong> {{ $pet->type ?? 'Unknown' }}</p>
                <p style="margin: 0 0 0.5rem 0; color: #333;"><strong>Breed:</strong> {{ $pet->breed ?? 'Mixed Breed' }}</p>
                <p style="margin: 0; color: #333;"><strong>Age:</strong> {{ $pet->age ?? 'Age Unknown' }}</p>
            </div>
        </div>

        <div style="flex: 2; min-width: 400px; display: flex; flex-direction: column; padding: 3rem;">
            
            <div style="border-bottom: 2px solid #f0ebe1; margin-bottom: 2rem; display: flex;">
                <button class="pet-tab active" onclick="switchTab('medical', this)">About & Medical Record</button>
                <button class="pet-tab" onclick="switchTab('pictures', this)">Pictures</button>
            </div>

            <div style="flex-grow: 1; overflow-y: auto; padding-right: 1rem;">
                
                <div id="tab-medical" class="tab-panel active">
                    <h3 style="color: #333; margin-bottom: 0.5rem;">The Story</h3>
                    <p style="color: #666; line-height: 1.7; margin-bottom: 2.5rem;">
                        {{ $pet->description ?? 'We are still getting to know this sweet animal!' }}
                    </p>

                    <h3 style="color: #333; margin-bottom: 0.5rem;">❤️ Health & Medical Record</h3>
                    <div style="background: #f8f9fa; border-left: 4px solid #e67e22; padding: 1.5rem; border-radius: 0 8px 8px 0;">
                        <p style="color: #555; margin: 0; line-height: 1.6;">
                            {{ $pet->health_summary ?? 'Routine health check completed. Vaccinations are up to date.' }}
                        </p>
                    </div>
                </div>

                <div id="tab-pictures" class="tab-panel">
                    <h3 style="color: #333; margin-bottom: 1.5rem;">Photo Gallery</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
                        <img src="{{ $imageUrl }}" style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onerror="this.src='https://placehold.co/400x400'">
                    </div>
                </div>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #f0ebe1;">
                @auth
                    @if($pet->status === 'Found a Home')
                        @if($userApplication && $userApplication->status === 'Approved for Adoption')
                            <div style="width: 100%; padding: 1.2rem; background: #D4EDDA; color: #155724; border: 2px solid #c3e6cb; border-radius: 12px; font-size: 1.2rem; font-weight: bold; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                🎉 You adopted {{ $pet->name }}!
                            </div>
                        @else
                            <button disabled style="width: 100%; padding: 1.2rem; background: #e2dcd3; color: #999; border: none; border-radius: 12px; font-size: 1.2rem; font-weight: bold; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                🏡 Already Adopted
                            </button>
                        @endif

                    @elseif($pet->status === 'Under Review')
                        @if($userApplication && in_array($userApplication->status, ['Pending', 'Under Review']))
                            <button disabled style="width: 100%; padding: 1.2rem; background: #FFF3CD; color: #856404; border: 2px solid #ffeeba; border-radius: 12px; font-size: 1.2rem; font-weight: bold; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                ⏳ Your application is Under Review
                            </button>
                        @else
                            <button disabled style="width: 100%; padding: 1.2rem; background: #e2dcd3; color: #999; border: none; border-radius: 12px; font-size: 1.2rem; font-weight: bold; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                🐾 Under Review by another adopter
                            </button>
                        @endif

                    @elseif($pet->status === 'Ready for Adoption')
                        @if($userApplication)
                            <button disabled style="width: 100%; padding: 1.2rem; background: #e2dcd3; color: #999; border: none; border-radius: 12px; font-size: 1.2rem; font-weight: bold; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                ✓ You have already applied
                            </button>
                        @else
                            <button type="button" onclick="
                                document.getElementById('apply_pet_id').value = '{{ $pet->id }}';
                                document.getElementById('apply_pet_name').innerText = '{{ addslashes($pet->name) }}';
                                document.getElementById('applicationDialog').showModal();
                            " style="width: 100%; padding: 1.2rem; background: #e67e22; color: white; border: none; border-radius: 12px; font-size: 1.2rem; font-weight: bold; cursor: pointer; box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3);">
                                Apply to Adopt {{ $pet->name }}
                            </button>
                        @endif
                    @else
                        <button disabled style="width: 100%; padding: 1.2rem; background: #e2dcd3; color: #999; border: none; border-radius: 12px; font-size: 1.2rem; font-weight: bold; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 10px;">
                            Not Available
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" style="display: block; text-align: center; width: 100%; padding: 1.2rem; background: #e67e22; color: white; text-decoration: none; border-radius: 12px; font-size: 1.2rem; font-weight: bold; box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3); box-sizing: border-box;">
                        Log in to Apply
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<dialog id="applicationDialog" style="border: none; border-radius: 20px; padding: 0; width: 90%; max-width: 500px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); margin: 0; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <div style="background: white; padding: 2.5rem; position: relative;">
        
        <button type="button" onclick="document.getElementById('applicationDialog').close()" style="position: absolute; top: 1.2rem; right: 1.5rem; font-size: 2rem; color: #999; cursor: pointer; background: transparent; border: none; outline: none; padding: 0; line-height: 1;">&times;</button>
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <h3 style="color: #e67e22; font-size: 1.8rem; margin-bottom: 0.5rem;">Apply for Adoption</h3>
            <p style="color: #666; font-size: 1rem;">You are applying to give <strong id="apply_pet_name" style="color: #333;">this pet</strong> a forever home!</p>
        </div>

        <form method="POST" action="{{ route('applications.store') }}">
            @csrf
            <input type="hidden" name="pet_id" id="apply_pet_id">

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444;">Home Address</label>
                <input type="text" name="adopter_address" required placeholder="123 Main St, City, State" style="width: 100%; padding: 0.9rem; border: 2px solid #eee; border-radius: 10px; font-size: 1rem;">
            </div>
            
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444;">Contact Number</label>
                <input type="text" name="contact_number" required placeholder="(555) 123-4567" style="width: 100%; padding: 0.9rem; border: 2px solid #eee; border-radius: 10px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444;">Why are you a good match? (Optional)</label>
                <textarea name="adopter_message" rows="3" style="width: 100%; padding: 0.9rem; border: 2px solid #eee; border-radius: 10px; font-size: 1rem; resize: vertical; font-family: inherit;"></textarea>
            </div>

            <button type="submit" style="width: 100%; padding: 1.2rem; background: #e67e22; color: white; border: none; border-radius: 10px; font-size: 1.1rem; font-weight: bold; cursor: pointer; box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3);">Submit Application</button>
        </form>
    </div>
</dialog>

<script>
    function switchTab(tabId, buttonElement) {
        document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
        document.querySelectorAll('.pet-tab').forEach(tab => tab.classList.remove('active'));
        document.getElementById('tab-' + tabId).classList.add('active');
        buttonElement.classList.add('active');
    }
</script>
@endsection