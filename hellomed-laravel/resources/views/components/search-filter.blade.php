@props(['action', 'searchPlaceholder' => 'Search...', 'filters' => []])

<div class="card search-filter-container" style="margin-bottom: 24px; padding: 16px; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; position: relative;">
    <form action="{{ $action }}" method="GET" class="search-filter-form" style="display: flex; flex-direction: column; gap: 16px;">
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            
            <!-- Search Input with Auto-suggestion Dropdown -->
            <div style="flex: 1; min-width: 250px; position: relative;" class="autocomplete-wrapper">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $searchPlaceholder }}" 
                       autocomplete="off" 
                       class="autocomplete-input"
                       style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; background: var(--input-bg); color: var(--text);">
                
                <div class="autocomplete-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--surface-raised); border: 1px solid var(--border); border-radius: 8px; margin-top: 4px; box-shadow: var(--shadow-lg); z-index: 50; max-height: 300px; overflow-y: auto;">
                    <div class="autocomplete-results"></div>
                </div>
            </div>

            <!-- Dynamic Filters -->
            @foreach($filters as $name => $options)
                @if(is_array($options))
                    <!-- Select Dropdown Filter -->
                    <div style="min-width: 150px;">
                        <select name="filters[{{ $name }}]" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; background: var(--input-bg); color: var(--text);">
                            <option value="">{{ ucfirst(str_replace('_', ' ', $name)) }} (All)</option>
                            @foreach($options as $val => $label)
                                <option value="{{ $val }}" @selected(request()->has("filters.$name") && (string)request("filters.$name") === (string)$val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif($options === 'date_range')
                    <!-- Date Range Filter -->
                    <div style="display: flex; gap: 8px; align-items: center; min-width: 250px;">
                        <input type="date" name="filters[{{ $name }}][start]" value="{{ request("filters.$name.start") }}" 
                               style="flex: 1; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; background: var(--input-bg); color: var(--text);" title="Start Date">
                        <span class="muted">to</span>
                        <input type="date" name="filters[{{ $name }}][end]" value="{{ request("filters.$name.end") }}" 
                               style="flex: 1; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; background: var(--input-bg); color: var(--text);" title="End Date">
                    </div>
                @endif
            @endforeach

            <!-- Action Buttons -->
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="button" style="padding: 10px 20px;">Search</button>
                @if(request()->has('search') || request()->has('filters'))
                    <a href="{{ $action }}" class="ghost-button" style="padding: 10px 20px; border-color: var(--border);">Clear</a>
                @endif
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.querySelector('.search-filter-container');
        if (!wrapper) return;
        
        const input = wrapper.querySelector('.autocomplete-input');
        const dropdown = wrapper.querySelector('.autocomplete-dropdown');
        const resultsContainer = wrapper.querySelector('.autocomplete-results');
        const actionUrl = wrapper.querySelector('.search-filter-form').getAttribute('action');
        
        let debounceTimer;

        input.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();
            
            if (query.length < 2) {
                dropdown.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`${actionUrl}?suggest=true&search=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    
                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<div style="padding: 12px 16px; color: var(--muted);">No matches found.</div>';
                    } else {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'autocomplete-item';
                            div.style.cssText = 'padding: 12px 16px; cursor: pointer; border-bottom: 1px solid var(--border-light);';
                            div.innerHTML = `<strong>${item.title || item.name || item.id}</strong> <span style="font-size: 12px; color: var(--muted); margin-left: 8px;">${item.subtitle || ''}</span>`;
                            
                            div.addEventListener('mouseover', () => {
                                div.style.background = 'var(--surface-hover)';
                            });
                            div.addEventListener('mouseout', () => {
                                div.style.background = 'transparent';
                            });
                            
                            div.addEventListener('click', () => {
                                input.value = item.title || item.name || item.id;
                                dropdown.style.display = 'none';
                                wrapper.querySelector('.search-filter-form').submit();
                            });
                            
                            resultsContainer.appendChild(div);
                        });
                    }
                    dropdown.style.display = 'block';
                })
                .catch(err => console.error('Suggestion fetch error:', err));
            }, 300);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    });
</script>
