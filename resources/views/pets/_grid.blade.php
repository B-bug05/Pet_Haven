@forelse ($pets as $pet)
    @php
        $imageUrl = 'https://images.unsplash.com/photo-1543852786-1cf6624b9987?q=80&w=400&auto=format&fit=crop';
        if ($pet->image) {
            if (\Illuminate\Support\Str::startsWith($pet->image, ['http://', 'https://'])) {
                $imageUrl = $pet->image;
            } else {
                $imageUrl = asset('storage/' . $pet->image);
            }
        } elseif (isset($pet->image_url)) {
            $imageUrl = $pet->image_url;
        }
    @endphp

    <div class="pet-card" style="position: relative; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
        
        <div style="position: absolute; top: 12px; left: 12px; z-index: 10; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            @if($pet->status === 'Ready for Adoption')
                background: #D4EDDA; color: #155724;
            @elseif($pet->status === 'Application Pending' || $pet->status === 'Under Review')
                background: #E0E7FF; color: #3730A3;
            @else
                background: #FFF3CD; color: #856404;
            @endif">
            {{ $pet->status }}
        </div>

        {{-- 🌟 SILENT AJAX FAVORITE BUTTON 🌟 --}}
        @auth
            @if(auth()->user()->role === 'adopter')
                <form action="{{ route('favorites.toggle', $pet->id) }}" method="POST" style="position: absolute; top: 12px; right: 12px; z-index: 10; margin: 0;"
                      onsubmit="event.preventDefault(); fetch(this.action, { method: 'POST', body: new FormData(this), headers: {'X-Requested-With': 'XMLHttpRequest'} }).then(() => { let span = this.querySelector('span'); if(span.innerText === '❤️') { span.innerText = '🤍'; span.style.color = '#ccc'; } else { span.innerText = '❤️'; span.style.color = '#e3342f'; } });">
                    @csrf
                    <button type="submit" style="background: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.15); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        @if(isset($favorites) && in_array($pet->id, $favorites))
                            <span style="color: #e3342f; font-size: 1.2rem; line-height: 1;">❤️</span>
                        @else
                            <span style="color: #ccc; font-size: 1.2rem; line-height: 1;">🤍</span>
                        @endif
                    </button>
                </form>
            @endif
        @endauth

        <img src="{{ $imageUrl }}" alt="{{ $pet->name }}" style="width: 100%; height: 220px; object-fit: cover;" onerror="this.src='https://placehold.co/400x400?text=Broken+Link'">
        
        <div class="pet-info" style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
            <h3 style="margin: 0 0 0.25rem 0; color: #333;">{{ $pet->name }}</h3>
            <p style="color: #e67e22; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.75rem;">
                {{ $pet->type }} • {{ $pet->age }}
            </p>
            
            <p style="color: #666; font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">
                {{ Str::limit($pet->description, 80, '...') }}
            </p>
            
            @auth
                <a href="{{ route('pets.show', $pet->id) }}" style="display: block; text-align: center; width: 100%; padding: 0.75rem; background: #e67e22; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                    Learn about {{ $pet->name }}
                </a>
            @else
                <button class="btn-meet" onclick="openLoginModalForPet('{{ route('pets.show', $pet->id) }}')" style="width: 100%; padding: 0.75rem; background: #e67e22; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Meet {{ $pet->name }}
                </button>
            @endauth
        </div>
    </div>
@empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
        <h3>No pets available right now.</h3>
    </div>
@endforelse