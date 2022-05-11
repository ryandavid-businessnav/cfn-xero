@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Sign up to CASHFLOWNAV Mobile with Xero!') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    @if (session('error_status'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error_status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- <div class="sso-wrapper" style="text-align:center;padding-bottom: 1rem;">
                        <a href="/manage/xero">
                            <button class="btn btn-primary">
                                <img src="data:image/svg+xml,%3Csvg viewBox='0 0 45 46' xmlns='http://www.w3.org/2000/svg'%3E %3Ctitle%3EXero%3C/title%3E %3Cpath fill='%2313B5EA' d='M22.457 45.49c12.402 0 22.456-10.072 22.456-22.495C44.913 10.57 34.86.5 22.457.5 10.054.5 0 10.57 0 22.995 0 35.418 10.054 45.49 22.457 45.49' /%3E %3Cpath fill='%23FFFFFF' d='M10.75 22.935l3.832-3.85a.688.688 0 0 0-.977-.965l-3.83 3.833-3.845-3.84a.687.687 0 0 0-.966.979l3.832 3.837-3.83 3.84a.688.688 0 1 0 .964.981l3.84-3.842 3.825 3.827a.685.685 0 0 0 1.184-.473.68.68 0 0 0-.2-.485l-3.83-3.846m22.782.003c0 .69.56 1.25 1.25 1.25a1.25 1.25 0 0 0-.001-2.5c-.687 0-1.246.56-1.246 1.25m-2.368 0c0-1.995 1.62-3.62 3.614-3.62 1.99 0 3.613 1.625 3.613 3.62s-1.622 3.62-3.613 3.62a3.62 3.62 0 0 1-3.614-3.62m-1.422 0c0 2.78 2.26 5.044 5.036 5.044s5.036-2.262 5.036-5.043c0-2.78-2.26-5.044-5.036-5.044a5.046 5.046 0 0 0-5.036 5.044m-.357-4.958h-.21c-.635 0-1.247.2-1.758.595a.696.696 0 0 0-.674-.54.68.68 0 0 0-.68.684l.002 8.495a.687.687 0 0 0 1.372-.002v-5.224c0-1.74.16-2.444 1.648-2.63.14-.017.288-.014.29-.014.406-.015.696-.296.696-.675a.69.69 0 0 0-.69-.688m-13.182 4.127c0-.02.002-.04.003-.058a3.637 3.637 0 0 1 7.065.055H16.2zm8.473-.13c-.296-1.403-1.063-2.556-2.23-3.296a5.064 5.064 0 0 0-5.61.15 5.098 5.098 0 0 0-1.973 5.357 5.08 5.08 0 0 0 4.274 3.767c.608.074 1.2.04 1.81-.12a4.965 4.965 0 0 0 1.506-.644c.487-.313.894-.727 1.29-1.222.006-.01.014-.017.022-.027.274-.34.223-.826-.077-1.056-.254-.195-.68-.274-1.014.156-.072.104-.153.21-.24.315-.267.295-.598.58-.994.802-.506.27-1.08.423-1.69.427-1.998-.023-3.066-1.42-3.447-2.416a3.716 3.716 0 0 1-.153-.58l-.01-.105h7.17c.982-.022 1.51-.717 1.364-1.51z' /%3E %3C/svg%3E" alt="Xero logo" aria-role="presentation" class="xero-sso-logo">
                                Sign in with Xero
                            </button>
                        </a>
                    </div> -->
                    @if(session('xeroOrg'))
                        {{-- dd(session('userInfo')) --}}
                        <form method="POST" action="/save-user">
                            @csrf
                            <fieldset>
                                <div class="text-center row mb-3">
                                    <legend><span class="number">1</span><span class="title">Basic Info</span></legend>
                                </div>
                                <div class="row mb-3">
                                    <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('First Name') }}</label>

                                    <div class="col-md-6">
                                        <input id="firstName" type="text" class="form-control" name="firstName" value="{{ session('jwtPayload.given_name') }}" required autocomplete="name" autofocus maxlength="20">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Last Name') }}</label>

                                    <div class="col-md-6">
                                        <input id="lastName" type="text" class="form-control" name="lastName" value="{{ session('jwtPayload.family_name') }}" required autocomplete="name" autofocus maxlength="20">
                                    </div>
                                </div>
                                

                                <div class="row mb-3">
                                    <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ session('jwtPayload.email') }}" required autocomplete="email" maxlength="40">
                                    </div>
                                </div>

                                <!-- <div class="row mb-3">
                                    <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Phone Number') }}</label>

                                    <div class="col-md-6">
                                        <input id="userPhoneNumber" type="text" class="form-control" name="userPhoneNumber" value="+61" required autocomplete="name" autofocus>
                                    </div>
                                </div> -->

                                <div class="row mb-3">
                                  <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Mobile Number') }}</label>
                                  <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                          <div class="input-group-text">+61</div>
                                        </div>
                                        <input type="text" class="form-control" name="userPhoneNumber" id="inlineFormInputGroup" placeholder="123456789" maxlength="10">
                                    </div>
                                  </div>
                                </div>
                            </fieldset>
                                
                             <fieldset>
                                <div class="text-center row mb-3">
                                    <legend><span class="number">2</span>Your Business</legend>
                                </div>
                                <div class="row mb-3">
                                    <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Business Name') }}</label>

                                    <div class="col-md-6">
                                        <input id="businessName" type="text" class="form-control @error('name') is-invalid @enderror" name="businessName" value="{{ session('xeroOrg.Name') }}" required autocomplete="name" autofocus>
                                    </div>
                                </div>

                                <!-- <div class="row mb-3">
                                    <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Phone Number') }}</label>

                                    <div class="col-md-6">
                                        <input id="phoneNumber" type="text" class="form-control" name="phoneNumber" value="{{ session('phoneNumber') }}" required autocomplete="name" autofocus>
                                    </div>
                                </div> -->

                                <div class="row mb-3">
                                  <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Phone Number') }}</label>
                                  <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="phoneNumber" type="text" class="form-control" name="phoneNumber" value="{{ session('phoneNumber') }}" autofocus maxlength="10">
                                    </div>
                                  </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Address') }}</label>

                                    <div class="col-md-6">
                                        <input id="address" type="text" class="form-control" name="address" maxlength="40" value="{{ session('xeroOrg.Addresses.0.AddressLine1').' '.session('xeroOrg.Addresses.0.City').' '.session('xeroOrg.Addresses.0.Region').','.session('xeroOrg.Addresses.0.Country').' '.session('xeroOrg.Addresses.0.PostalCode') }}" required autocomplete="name" autofocus>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Number of Employees') }}</label>

                                    <div class="col-md-6">
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="numberOfEmployees" id="exampleRadios1" value="01-9" checked>
                                          <label class="form-check-label" for="exampleRadios1">
                                            1-9
                                          </label>
                                        </div>
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="numberOfEmployees" id="exampleRadios2" value="10-49">
                                          <label class="form-check-label" for="exampleRadios2">
                                            10-49
                                          </label>
                                        </div>
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="numberOfEmployees" id="exampleRadios2" value="50-99">
                                          <label class="form-check-label" for="exampleRadios2">
                                            50-99
                                          </label>
                                        </div>
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="numberOfEmployees" id="exampleRadios2" value="100-499">
                                          <label class="form-check-label" for="exampleRadios2">
                                            100-499
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="numberOfEmployees" id="exampleRadios2" value="500+">
                                          <label class="form-check-label" for="exampleRadios2">
                                            500+
                                          </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Default Currency') }}</label>

                                    <div class="col-md-6">
                                        <select class="form-control" name="currency">
                                            <option>AUD - Australian Dollar</option>
                                            <option>EUR - Euro</option>
                                            <option>USD - US Dollar</option>
                                            <option>NZD - New Zealand Dollar</option>
                                            <option>GBP - Sterling Pound</option>
                                            <option>MXN - Mexican Peso</option>
                                            <option>CAD - Canadian Dollar</option>
                                            <option>OTHER</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="anzic_code" class="col-md-4 col-form-label text-md-end">{{ __('ANZSIC Code') }}</label>

                                    <div class="col-md-6">
                                        <select class="form-control" name="anzic_code" v-model="anzic">
                                            <option value="agriculture_forestry_fishing">Agriculture, forestry and fishing</option> 
                                            <option value="mining" >Mining</option> 
                                            <option value="manufacturing" >Manufacturing</option> 
                                            <option value="electricity_gas_water_waste_services" >Electricity, gas, water and waste services</option> 
                                            <option value="construction" >Construction</option> 
                                            <option value="wholesale_trade" >Wholesale trade</option> 
                                            <option value="retail_trade" >Retail trade</option> 
                                            <option value="accommodation_food_services" >Accommodation and food services</option> 
                                            <option value="transport_postal_warehousing" >Transport, postal and warehousing</option> 
                                            <option value="information_media_telecommunications" >Information media and telecommunications</option> 
                                            <option value="financial_insurance_services" >Financial and insurance services</option> 
                                            <option value="rental_hiring_real_estate_services" >Rental hiring and real estate services</option> 
                                            <option value="professional_scientific_technical_services" >Professional, scientific and technical services</option> 
                                            <option value="administrative_support_services" >Administrative and support services</option> 
                                            <option value="public_administration_safety" >Public administration and safety</option> 
                                            <option value="education_training" >Education and training</option> 
                                            <option value="health_care_social_assistance" >Health care and social assistance</option> 
                                            <option value="arts_recreation_services" >Arts and recreation services</option> 
                                            <option value="other_services">Other services</option> 
                                        </select>
                                    </div>
                                </div>

                                 <div class="row mb-3" v-if="anzic">
                                    <label for="anzic_subdivision" class="col-md-4 col-form-label text-md-end">{{ __('ANZSIC Sub Division') }}</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'agriculture_forestry_fishing'">
                                            <option>Agriculture</option>
                                            <option>Aquaculture</option>
                                            <option>Forestry and Logging</option>
                                            <option>Fishing, Hunting and Trapping</option>
                                            <option>Agriculture, Forestry, and Fishing Support Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'mining'">
                                            <option>Coal Mining</option>
                                            <option>Oil and Gas Extraction</option>
                                            <option>Metal Ore Mining</option>
                                            <option>Non-Metallic Minteral Mining and Quarrying</option>
                                            <option>Exploration and Other Mining Support Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'manufacturing'">
                                            <option>Food Product Manufacturing</option>
                                            <option>Beverage and Tobacco Product Manufacturing</option>
                                            <option>Textile, Leather, Clothing, and Footwear Manufacturing</option>
                                            <option>Wool Product Manufacturing</option>
                                            <option>Pulp, Paper, and Converted Paper Product Manufacturing</option>
                                            <option>Printing (including the Reproduction of Recorded Media)</option>
                                            <option>Basic Chemical and Chemical Product Manufacturing</option>
                                            <option>Polymer Product and Rubber Product Manufacturing</option>
                                            <option>Non-Metallic Mineral Product Manufacturing</option>
                                            <option>Primary Metal and Metal Product Manufacturing</option>
                                            <option>Fabricated Metal Product Manufacturing</option>
                                            <option>Transport Equipment Manufacturing</option>
                                            <option>Machinery and Equipment Manufacturing</option>
                                            <option>Furniture and Other Manufacturing</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'electricity_gas_water_waste_services'">
                                            <option>Electrical Supply</option>
                                            <option>Gas Supply</option>
                                            <option>Water Supply, Sewerage, and Drainage Services</option>
                                            <option>Waste Collection, Treatment, and Disposal Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'construction'">
                                            <option>Building Construction>/option>
                                            <option>Heavy and Civil Engineering Construction>/option>
                                            <option>Construction Services>/option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'wholesale_trade'">
                                            <option>Basic Material Wholesaling</option>
                                            <option>Machinery and Equipment Wholsaling</option>
                                            <option>Motor Vehicle and Motor Vehicle Parts Wholesaling</option>
                                            <option>Grocery, Liquoe, and Tobacco Product Wholesaling</option>
                                            <option>Other Goods Wholesaling</option>
                                            <option>Commission-Based Wholesaling</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'retail_trade'">
                                            <option>Motor Vehicle and Motor Vehicle Parts Retailing</option>
                                            <option>Fuel Retailing</option>
                                            <option>Food Retailing</option>
                                            <option>Other Store-Based Retailing</option>
                                            <option>Food and Beverage Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'accommodation_food_services'">
                                            <option>Accommodation</option>
                                            <option>Food and Beverage Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'transport_postal_warehousing'">
                                            <option>Road Transport</option>
                                            <option>Rail Transport</option>
                                            <option>Water Transport</option>
                                            <option>Air and Space Transport</option>
                                            <option>Other Transport</option>
                                            <option>Postal and Courier Pick-up and Delivery Services</option>
                                            <option>Transport Support Services</option>
                                            <option>Warehousing and Storage Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'information_media_telecommunications'">
                                            <option>Publishing (except Internet and Music Publishing)</option>
                                            <option>Motion Picture and Sound Recording Activities</option>
                                            <option>Broadcasting (except Internet)</option>
                                            <option>Internet Publishing and Broadcasting</option>
                                            <option>Telecommunications Services</option>
                                            <option>Internet Service Providers, Web Search Portals, and Data Processing Services</option>
                                            <option>Library and Other Information Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'financial_insurance_services'">
                                            <option>Finance</option>
                                            <option>Insurance and Superannuation Funds</option>
                                            <option>Auxiliary Finance and Insurance Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'rental_hiring_real_estate_services'">
                                            <option>Rental and Hiring Services (except Real Estate)</option>
                                            <option>Property Operators and Real Estate Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'professional_scientific_technical_services'">
                                            <option>Professional, Scientific, and Technical Services (Except Computer System Design and Related Systems)</option>
                                            <option>Computer System Design and Related Systems</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'administrative_support_services'">
                                            <option>Administrative Services</option>
                                            <option>Building Cleaning, Pest Control, and Other Support Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'public_administration_safety'">
                                            <option>Public Administration</option>
                                            <option>Defence</option>
                                            <option>Public Order, Safety, and Regulator Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'education_training'">
                                            <option>Preschool and School Education</option>
                                            <option>Tertiary Education</option>
                                            <option>Adult, Community, and Other Education</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'health_care_social_assistance'">
                                            <option>Hospitals</option>
                                            <option>Medical and Other Health Care Services</option>
                                            <option>Residential Care Services</option>
                                            <option>Social Assistance Services</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'arts_recreation_services'">
                                            <option>Heritage Activities</option>
                                            <option>Creative and Performing Arts Activities</option>
                                            <option>Sports and Recreation Activities</option>
                                            <option>Gambling Activities</option>
                                        </select>

                                        <select class="form-control" name="anzic_subdivision" v-if="anzic == 'other_services'">
                                            <option>Repair and Maintenance</option>
                                            <option>Personal and Other Services</option>
                                            <option>Private Households Employing Staff and Undifferentiated Goods - and Service-Producing Activities of Households for Own Use</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" maxlength="20" minlength="6">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>


                                <br/> 
                                <div class="row mb-3">
                                    <div class="col-md-6 offset-md-3" style="padding-left:6%">
                                        <a href="https://businessnav.com/cashflownav-terms/" class="col-md-3">Terms and Conditions | </a>
                                        <a href="https://businessnav.com/cashflownav-privacy/" class="col-md-3">Privacy and Policy</a>
                                    </div>
                                </div>

                            </fieldset>
                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-5">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
