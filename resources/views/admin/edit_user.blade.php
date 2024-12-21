@extends('admin.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y p-0">
        <div class="row">
            <div class="col mb-4 order-0">
                <div class="d-flex align-items-end row">
                    <div class="col">
                        <div class="card mb-4 mx-auto">
                            <div class="card-header mb-3 d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Edit User Form</h3>
                            </div>
                            <div class="card-body">
                                <!-- Form -->
                                <form id="dataForm" method="post" enctype="multipart/form-data"
                                    action="{{ route('updateUser', $user->id) }}">
                                    @csrf
                                    <div class="mb-5">
                                        <label for="image" class="mb-3 form-label fw-bold text-uppercase">Upload
                                            Profile Photo:</label>
                                        <input type="file" class="form-control" id="image" name="image">
                                        <span class="text-muted">
                                            Current Selected Profile Photo: {{ $user->user_profile->image }}<br>
                                        </span>
                                        <span class="error">
                                            @error('image')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label for="f_name" class="mb-3 form-label fw-bold text-uppercase">First
                                            Name</label>
                                        <input type="text" class="form-control" id="f_name" name="f_name"
                                            value="{{ $user->f_name }}" placeholder="Enter your first name">
                                        <span class="error">
                                            @error('f_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label for="l_name" class="mb-3 form-label fw-bold text-uppercase">Last
                                            Name</label>
                                        <input type="text" class="form-control" id="l_name" name="l_name"
                                            value="{{ $user->l_name }}" placeholder="Enter your last name">
                                        <span class="error">
                                            @error('l_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label for="email" class="mb-3 form-label fw-bold text-uppercase">Email</label>
                                        <input type="text" class="form-control" id="email" name="email"
                                            value="{{ $user->email }}"
                                            placeholder="Enter your email (e.g., user@example.com)" readonly>
                                        <span class="error">
                                            @error('email')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="mb-5">
                                        <label class="mb-3 form-label fw-bold text-uppercase" for="old_password">Old
                                            Password</label>
                                        <input type="password" id="old_password" class="form-control" name="old_password"
                                            value="{{ old('old_password') }}" placeholder="Enter old password" />
                                        <span class="error">
                                            @error('old_password')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label class="mb-3 form-label fw-bold text-uppercase" for="new_password">New
                                            Password</label>
                                        <input type="password" id="new_password" class="form-control" name="new_password"
                                            value="{{ old('new_password') }}" placeholder="Enter new password" />
                                        <span class="error">
                                            @error('new_password')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label class="mb-3 form-label fw-bold text-uppercase" for="confirm_password">Confirm
                                            Password</label>
                                        <input type="password" id="confirm_password" class="form-control"
                                            name="confirm_password" value="{{ old('confirm_password') }}"
                                            placeholder="Confirm new password" />
                                        <span class="error">
                                            @error('confirm_password')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>


                                    <div class="mb-5">
                                        <label for="phone" class="mb-3 form-label fw-bold text-uppercase">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ $user->user_profile->phone }}" placeholder="Enter your phone number">
                                        <span class="error">
                                            @error('phone')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label for="address" class="mb-3 form-label fw-bold text-uppercase">Address</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            value="{{ $user->user_profile->address }}" placeholder="Enter your address">
                                        <span class="error">
                                            @error('address')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="country" class="form-label fw-bold text-uppercase">Country</label>
                                        <div class="d-flex align-items-center">
                                            <span id="country-spinner" class="spinner-border text-primary me-2"
                                                style="display:none;" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </span>
                                            <select id="country" name="country" class="form-control"
                                                onchange="populateStates()">
                                                <option value="">Select a country</option>
                                                <!-- Options for countries will be added dynamically -->
                                            </select>
                                        </div>
                                        <span class="error">
                                            @error('country')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="state" class="form-label fw-bold text-uppercase">State</label>
                                        <div class="d-flex align-items-center">
                                            <span id="state-spinner" class="spinner-border text-primary me-2"
                                                style="display:none;" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </span>
                                            <select id="state" name="state" class="form-control"
                                                onchange="populateCities()" disabled>
                                                <option value="">Select a state</option>
                                                <!-- States will be populated dynamically based on country selection -->
                                            </select>
                                        </div>
                                        <span class="error">
                                            @error('state')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="city" class="form-label fw-bold text-uppercase">City</label>
                                        <div class="d-flex align-items-center">
                                            <span id="city-spinner" class="spinner-border text-primary me-2"
                                                style="display:none;" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </span>
                                            <select id="city" name="city" class="form-control" disabled>
                                                <option value="">Select a city</option>
                                                <!-- Cities will be populated dynamically based on state selection -->
                                            </select>
                                        </div>
                                        <span class="error">
                                            @error('city')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>


                                    <!-- Additional Fields here with similar pattern -->

                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const countries = {
            "Pakistan": {
                "Sindh": ["Karachi", "Hyderabad", "Sukkur"],
                "Punjab": ["Lahore", "Rawalpindi", "Multan"],
                "Khyber Pakhtunkhwa": ["Peshawar", "Mardan", "Abbottabad"],
                "Balochistan": ["Quetta", "Gwadar", "Turbat"],
            },
            "India": {
                "Gujarat": ["Ahmedabad", "Rajkot", "Jamnagar"],
                "Haryana": ["Faridabad", "Panipat", "Fatehabad"],
                "Uttar Pradesh": ["Aligarh", "Agra", "Lucknow"],
                "West Bengal": ["Kolkata", "Howrah", "Darjeeling"],
            },
            "United States of America": {
                "California": ["Los Angeles", "San Francisco", "San Diego"],
                "Texas": ["Houston", "Austin", "Dallas"],
                "New York": ["New York City", "Buffalo", "Albany"],
                "Florida": ["Miami", "Orlando", "Tampa"],
            },
            "United Kingdom": {
                "England": ["London", "Manchester", "Liverpool"],
                "Scotland": ["Edinburgh", "Glasgow", "Aberdeen"],
                "Wales": ["Cardiff", "Swansea", "Newport"],
                "Northern Ireland": ["Belfast", "Londonderry", "Lisburn"],
            },
            "Australia": {
                "New South Wales": ["Sydney", "Newcastle", "Wollongong"],
                "Victoria": ["Melbourne", "Geelong", "Ballarat"],
                "Queensland": ["Brisbane", "Gold Coast", "Townsville"],
                "Western Australia": ["Perth", "Fremantle", "Bunbury"]
            },
        };

        // Function to populate the country select dropdown
        function populateCountries() {
            const countrySelect = document.getElementById('country');
            for (const country in countries) {
                const option = document.createElement('option');
                option.value = country;
                option.textContent = country;
                countrySelect.appendChild(option);
            }
            const selectedCountry = "{{ old('country', $user->user_profile->country ?? '') }}"; // Retain selected value
            if (selectedCountry) {
                countrySelect.value = selectedCountry;
                populateStates();
            }
        }

        // Function to populate the state select dropdown based on selected country
        function populateStates() {
            const countrySelect = document.getElementById('country');
            const stateSelect = document.getElementById('state');
            const selectedCountry = countrySelect.value;

            const stateSpinner = document.getElementById('state-spinner');
            stateSelect.innerHTML = '<option value="">Select a state</option>'; // Clear previous options

            if (selectedCountry) {
                showSpinner(stateSpinner); // Show spinner

                // Simulate loading time (2ms)
                setTimeout(() => {
                    const states = Object.keys(countries[selectedCountry]);
                    states.forEach(state => {
                        const option = document.createElement('option');
                        option.value = state;
                        option.textContent = state;
                        stateSelect.appendChild(option);
                    });
                    hideSpinner(stateSpinner); // Hide spinner
                    stateSelect.disabled = false; // Enable state dropdown

                    // Retain selected value
                    const selectedState = "{{ old('state', $user->user_profile->state ?? '') }}";
                    if (selectedState) {
                        stateSelect.value = selectedState;
                        populateCities();
                    }
                }, 2); // 2ms delay
            }
        }

        // Function to populate the city select dropdown based on selected state
        function populateCities() {
            const countrySelect = document.getElementById('country');
            const stateSelect = document.getElementById('state');
            const citySelect = document.getElementById('city');
            const selectedCountry = countrySelect.value;
            const selectedState = stateSelect.value;

            const citySpinner = document.getElementById('city-spinner');
            citySelect.innerHTML = '<option value="">Select a city</option>'; // Clear previous options

            if (selectedCountry && selectedState) {
                showSpinner(citySpinner); // Show spinner

                // Simulate loading time (2ms)
                setTimeout(() => {
                    const cities = countries[selectedCountry][selectedState];
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                    hideSpinner(citySpinner); // Hide spinner
                    citySelect.disabled = false; // Enable city dropdown

                    // Retain selected value
                    const selectedCity = "{{ old('city', $user->user_profile->city ?? '') }}";
                    if (selectedCity) {
                        citySelect.value = selectedCity;
                    }
                }, 2); // 2ms delay
            }
        }

        // Show spinner
        function showSpinner(spinner) {
            spinner.style.display = 'inline-block';
        }

        // Hide spinner
        function hideSpinner(spinner) {
            spinner.style.display = 'none';
        }

        // Initialize country dropdown when the page loads
        window.onload = function() {
            populateCountries();
        };
    </script>
@endsection
