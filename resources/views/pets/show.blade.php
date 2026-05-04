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
                <button type="button" class="pet-tab active" onclick="switchTab('medical', this)">About & Medical Record</button>
                <button type="button" class="pet-tab" onclick="switchTab('pictures', this)">Pictures</button>
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
                    @if($pet->photos->count() > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
                            {{-- Main profile photo first --}}
                            <img src="{{ $imageUrl }}"
                                style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                                onerror="this.src='https://placehold.co/400x400'">
                            {{-- Gallery photos --}}
                            @foreach($pet->photos as $photo)
                                <img src="{{ asset('storage/' . $photo->image) }}"
                                    style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                                    onerror="this.src='https://placehold.co/400x400'">
                            @endforeach
                        </div>
                    @else
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
                            <img src="{{ $imageUrl }}"
                                style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                                onerror="this.src='https://placehold.co/400x400'">
                        </div>
                    @endif
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

                    @elseif($pet->status === 'Ready for Adoption')
                        @if($userApplication)
                            <button disabled style="width: 100%; padding: 1.2rem; background: #FFF3CD; color: #856404; border: 2px solid #ffeeba; border-radius: 12px; font-size: 1.2rem; font-weight: bold; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                ⏳ Your application is under review
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

<dialog id="applicationDialog" style="border: none; border-radius: 20px; padding: 0; width: 90%; max-width: 560px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); margin: 0; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); max-height: 90vh; overflow: hidden;">
    <div style="background: white; position: relative; display: flex; flex-direction: column; max-height: 90vh;">

        {{-- Header --}}
        <div style="padding: 2rem 2.5rem 1.5rem; border-bottom: 1px solid #f0ebe1; flex-shrink: 0;">
            <button type="button" onclick="document.getElementById('applicationDialog').close()"
                style="position: absolute; top: 1.2rem; right: 1.5rem; font-size: 2rem; color: #999; cursor: pointer; background: transparent; border: none; outline: none; padding: 0; line-height: 1;">&times;</button>
            <h3 style="color: #e67e22; font-size: 1.6rem; margin: 0 0 0.25rem 0;">Apply for Adoption</h3>
            <p style="color: #666; font-size: 0.9rem; margin: 0;">Applying for <strong id="apply_pet_name" style="color: #333;">this pet</strong></p>

            {{-- Step indicator --}}
            <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                <div id="step-dot-1" style="height: 4px; flex: 1; border-radius: 4px; background: #e67e22; transition: background 0.3s;"></div>
                <div id="step-dot-2" style="height: 4px; flex: 1; border-radius: 4px; background: #e2dcd3; transition: background 0.3s;"></div>
                <div id="step-dot-3" style="height: 4px; flex: 1; border-radius: 4px; background: #e2dcd3; transition: background 0.3s;"></div>
            </div>
            <p id="step-label" style="font-size: 0.75rem; color: #999; margin: 0.4rem 0 0 0;">Step 1 of 3 — Contact Details</p>
        </div>

        {{-- Scrollable form body --}}
        <div style="overflow-y: auto; flex: 1; padding: 1.5rem 2.5rem;">
            <form method="POST" action="{{ route('applications.store') }}" id="applicationForm">
                @csrf
                <input type="hidden" name="pet_id" id="apply_pet_id">

                {{-- STEP 1: Contact Details --}}
                <div id="app-step-1">
                    <div style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444; font-size: 0.9rem;">Home Address</label>
                        <input type="text" name="adopter_address" id="f_address" required placeholder="123 Main St, City"
                            style="width: 100%; padding: 0.85rem; border: 2px solid #eee; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444; font-size: 0.9rem;">Contact Number</label>
                        <input type="text" name="contact_number" id="f_contact" required placeholder="(555) 123-4567"
                            style="width: 100%; padding: 0.85rem; border: 2px solid #eee; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444; font-size: 0.9rem;">Housing Type</label>
                        <select name="housing_type" id="f_housing" required
                            style="width: 100%; padding: 0.85rem; border: 2px solid #eee; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; background: white;">
                            <option value="">Select one...</option>
                            <option value="Own house with yard">Own house with yard</option>
                            <option value="Own house without yard">Own house without yard</option>
                            <option value="Renting house">Renting house</option>
                            <option value="Apartment/Condo">Apartment / Condo</option>
                            <option value="Living with family">Living with family</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444; font-size: 0.9rem;">If renting, does your landlord allow pets?</label>
                        <select name="landlord_allows_pets" id="f_landlord"
                            style="width: 100%; padding: 0.85rem; border: 2px solid #eee; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; background: white;">
                            <option value="N/A - I own my home">N/A — I own my home</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                            <option value="Not sure">Not sure</option>
                        </select>
                    </div>
                </div>

                {{-- STEP 2: Lifestyle Questions --}}
                <div id="app-step-2" style="display: none;">
                    <div style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444; font-size: 0.9rem;">Do you have other pets at home?</label>
                        <select name="has_other_pets" id="f_other_pets" required
                            style="width: 100%; padding: 0.85rem; border: 2px solid #eee; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; background: white;">
                            <option value="">Select one...</option>
                            <option value="No other pets">No other pets</option>
                            <option value="Yes - dogs">Yes — dogs</option>
                            <option value="Yes - cats">Yes — cats</option>
                            <option value="Yes - both dogs and cats">Yes — both dogs and cats</option>
                            <option value="Yes - other animals">Yes — other animals</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444; font-size: 0.9rem;">Do you have outdoor space (yard, garden, etc.)?</label>
                        <select name="has_outdoor_space" id="f_outdoor" required
                            style="width: 100%; padding: 0.85rem; border: 2px solid #eee; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; background: white;">
                            <option value="">Select one...</option>
                            <option value="Yes - large yard">Yes — large yard</option>
                            <option value="Yes - small yard/garden">Yes — small yard / garden</option>
                            <option value="Balcony only">Balcony only</option>
                            <option value="No outdoor space">No outdoor space</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444; font-size: 0.9rem;">How many hours per day will the pet be alone?</label>
                        <select name="hours_alone" id="f_hours" required
                            style="width: 100%; padding: 0.85rem; border: 2px solid #eee; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; background: white;">
                            <option value="">Select one...</option>
                            <option value="0-2 hours">0–2 hours</option>
                            <option value="2-4 hours">2–4 hours</option>
                            <option value="4-6 hours">4–6 hours</option>
                            <option value="6-8 hours">6–8 hours</option>
                            <option value="More than 8 hours">More than 8 hours</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444; font-size: 0.9rem;">Previous pet ownership experience?</label>
                        <select name="previous_pet_experience" id="f_experience" required
                            style="width: 100%; padding: 0.85rem; border: 2px solid #eee; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; background: white;">
                            <option value="">Select one...</option>
                            <option value="First time owner">First time owner</option>
                            <option value="Previously owned pets">Previously owned pets</option>
                            <option value="Currently own other pets">Currently own other pets</option>
                            <option value="Professional experience (vet, trainer, etc.)">Professional experience (vet, trainer, etc.)</option>
                        </select>
                    </div>
                </div>

                {{-- STEP 3: Personal Statement + T&C --}}
                <div id="app-step-3" style="display: none;">
                    <div style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444; font-size: 0.9rem;">Why do you want to adopt this specific pet? <span style="color: #999; font-weight: 400;">(required)</span></label>
                        <textarea name="why_this_pet" id="f_why" required rows="3"
                            style="width: 100%; padding: 0.85rem; border: 2px solid #eee; border-radius: 10px; font-size: 0.95rem; resize: vertical; font-family: inherit; box-sizing: border-box;"
                            placeholder="Tell us what drew you to this pet and why you'd be a great match..."></textarea>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #444; font-size: 0.9rem;">Anything else you'd like us to know? <span style="color: #999; font-weight: 400;">(optional)</span></label>
                        <textarea name="adopter_message" rows="2"
                            style="width: 100%; padding: 0.85rem; border: 2px solid #eee; border-radius: 10px; font-size: 0.95rem; resize: vertical; font-family: inherit; box-sizing: border-box;"
                            placeholder="e.g. family members, work schedule, special circumstances..."></textarea>
                    </div>

                    {{-- Terms & Conditions --}}
                    <div style="background: #fdfaf6; border: 1.5px solid #f0ebe1; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
                        <div style="max-height: 100px; overflow-y: auto; margin-bottom: 1rem; font-size: 0.82rem; color: #666; line-height: 1.6; padding-right: 0.5rem;">
                            <strong style="color: #333; display: block; margin-bottom: 0.5rem;">Adoption Terms & Conditions</strong>
                            By submitting this application, you agree to the following:
                            <ul style="margin: 0.5rem 0 0 1.2rem; padding: 0;">
                                <li>You are at least 18 years of age and legally able to enter into this agreement.</li>
                                <li>The pet will be kept in a safe, loving, and appropriate environment at all times.</li>
                                <li>You will provide proper veterinary care, nutrition, and shelter for the pet.</li>
                                <li>You will not transfer, sell, or give away the pet without notifying PetHaven.</li>
                                <li>PetHaven reserves the right to conduct a follow-up welfare check on the pet.</li>
                                <li>Submitting an application does not guarantee adoption — all applications are subject to staff review and approval.</li>
                            </ul>
                        </div>
                        <label style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" name="terms_agreed" value="1" required
                                style="margin-top: 3px; width: 17px; height: 17px; accent-color: #e67e22; cursor: pointer; flex-shrink: 0;">
                            <span style="font-size: 0.88rem; color: #444; line-height: 1.5;">
                                I have read and agree to the <strong style="color: #e67e22;">Adoption Terms & Conditions</strong> above.
                            </span>
                        </label>
                    </div>
                </div>

            </form>
        </div>

        {{-- Footer navigation --}}
        <div style="padding: 1.25rem 2.5rem; border-top: 1px solid #f0ebe1; display: flex; gap: 0.75rem; flex-shrink: 0;">
            <button type="button" id="app-btn-back" onclick="appStepBack()" style="display: none; flex: 1; padding: 0.9rem; background: #f5f5f5; color: #555; border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 600; cursor: pointer;">
                ← Back
            </button>
            <button type="button" id="app-btn-next" onclick="appStepNext()"
                style="flex: 2; padding: 0.9rem; background: #e67e22; color: white; border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 15px rgba(230,126,34,0.3);">
                Next →
            </button>
            <button type="submit" form="applicationForm" id="app-btn-submit" style="display: none; flex: 2; padding: 0.9rem; background: #e67e22; color: white; border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 15px rgba(230,126,34,0.3);">
                Submit Application 🐾
            </button>
        </div>
    </div>
</dialog>

<script>
    let appCurrentStep = 1;
    const appTotalSteps = 3;

    function appUpdateStepUI() {
        for (let i = 1; i <= appTotalSteps; i++) {
            document.getElementById('app-step-' + i).style.display = i === appCurrentStep ? 'block' : 'none';
            document.getElementById('step-dot-' + i).style.background = i <= appCurrentStep ? '#e67e22' : '#e2dcd3';
        }
        const labels = ['Step 1 of 3 — Contact Details', 'Step 2 of 3 — Your Lifestyle', 'Step 3 of 3 — Personal Statement'];
        document.getElementById('step-label').textContent = labels[appCurrentStep - 1];
        document.getElementById('app-btn-back').style.display = appCurrentStep > 1 ? 'block' : 'none';
        document.getElementById('app-btn-next').style.display = appCurrentStep < appTotalSteps ? 'block' : 'none';
        document.getElementById('app-btn-submit').style.display = appCurrentStep === appTotalSteps ? 'block' : 'none';
    }

    function appStepNext() {
        // Validate current step fields before advancing
        const stepFields = {
            1: ['f_address', 'f_contact', 'f_housing'],
            2: ['f_other_pets', 'f_outdoor', 'f_hours', 'f_experience'],
        };
        if (stepFields[appCurrentStep]) {
            for (const id of stepFields[appCurrentStep]) {
                const el = document.getElementById(id);
                if (el && !el.value.trim()) {
                    el.style.borderColor = '#e3342f';
                    el.focus();
                    return;
                }
                if (el) el.style.borderColor = '#eee';
            }
        }
        if (appCurrentStep < appTotalSteps) {
            appCurrentStep++;
            appUpdateStepUI();
        }
    }

    function appStepBack() {
        if (appCurrentStep > 1) {
            appCurrentStep--;
            appUpdateStepUI();
        }
    }

    // Reset to step 1 every time the dialog opens
    document.getElementById('applicationDialog').addEventListener('close', function() {
        appCurrentStep = 1;
        appUpdateStepUI();
        document.getElementById('applicationForm').reset();
    });
</script>
@endsection