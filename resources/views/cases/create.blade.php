@extends('adminlte::page')

@section('title', 'Create Case')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Create Case</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cases.index') }}">Cases</a></li>
                <li class="breadcrumb-item active">Create Case</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('cases.create')
                <div class="card">
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('cases.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="client_id">Client</label>

                                    @if ($clients->isNotEmpty())
                                        <select name="client_id" class="form-control select2" required>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select name="client_id" id="clientSelect" class="form-control select2" required></select>
                                    @endif
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="title">Case Title</label>
                                    <input type="text" name="title" class="form-control" required value="{{ old('title') }}" placeholder="Enter case title">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="status">Case Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>

                                {{-- Category --}}
                                <div class="form-group col-md-6">
                                    <label for="category">Case Category</label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="civil" {{ old('category') == 'civil' ? 'selected' : '' }}>Civil</option>
                                        <option value="criminal" {{ old('category') == 'criminal' ? 'selected' : '' }}>Criminal</option>
                                    </select>
                                </div>

                                {{-- Subcategory --}}
                                <div class="form-group col-md-6">
                                    <label for="subcategory">Case Subcategory</label>
                                    <select name="subcategory" id="subcategory" class="form-control" required>
                                        <option value="">-- Select Subcategory --</option>
                                        {{-- Options populated by JS --}}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Case Description</label>
                                <textarea name="description" rows="4" class="form-control" required placeholder="Enter detailed case description">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="voice_note">Voice Note</label>

                                <div id="recorder-container" class="text-center">
                                    <button type="button" id="recordBtn" class="btn btn-light border border-secondary rounded-circle p-4 mb-2 shadow-sm">
                                        <i class="fas fa-microphone fa-lg text-danger" id="recordIcon"></i>
                                    </button>
                                    <p id="recording-status" class="text-danger" style="display: none;">Recording... Tap again to stop</p>

                                    <audio id="audioPlayback" controls style="display: none; width: 100%;"></audio>
                                </div>

                                <input type="hidden" name="voice_note_blob" id="voice_note_blob">
                            </div>

                            @can('cases.create')
                                <button type="submit" class="btn btn-success">Create Case</button>
                            @endcan

                            <a href="{{ route('cases.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        #recorder-container button {
            width: 80px;
            height: 80px;
        }

        #recordBtn:hover {
            background-color: #f5f5f5;
        }

        .select2-container .select2-selection--single {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            height: 37px; /* Set to your desired height */
            user-select: none;
            -webkit-user-select: none;
        }

        /* Adjust the line-height of the rendered text inside Select2 */
        .select2-container--classic .select2-selection--single .select2-selection__rendered {
            line-height: 34px; /* Adjust line height */
        }

        /* Style the Select2 arrow and set its height */
        .select2-container--classic .select2-selection--single .select2-selection__arrow {
            background-color: #ddd;
            border: none;
            border-left: 1px solid #aaa;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            height: 35px; /* Set height for the arrow */
            position: absolute;
            top: 1px;
            right: 1px;
            width: 20px;
            background-image: -webkit-linear-gradient(top, #eeeeee 50%, #cccccc 100%);
            background-image: -o-linear-gradient(top, #eeeeee 50%, #cccccc 100%);
            background-image: linear-gradient(to bottom, #eeeeee 50%, #cccccc 100%);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFEEEEEE', endColorstr='#FFCCCCCC', GradientType=0);
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize select2 on the roles select
            $('.select2').select2({
                placeholder: "Select a client",
                allowClear: true
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#clientSelect').select2({
                placeholder: 'Enter client email...',
                ajax: {
                    url: '{{ route("clients.searchByEmail") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 5
            });
        });
    </script>

    <script>
        let mediaRecorder;
        let audioChunks = [];
        let isRecording = false;
        let wavesurfer;

        const recordBtn = document.getElementById('recordBtn');
        const recordIcon = document.getElementById('recordIcon');
        const statusText = document.getElementById('recording-status');
        const audioPlayback = document.getElementById('audioPlayback');
        const hiddenInput = document.getElementById('voice_note_blob');

        function initWaveSurfer(blobUrl = null) {
            if (wavesurfer) {
                wavesurfer.destroy();
            }

            if (blobUrl) {
                wavesurfer.load(blobUrl);
            }
        }

        recordBtn.addEventListener('click', async () => {
            if (!isRecording) {
                if (!navigator.mediaDevices) {
                    alert("Your browser doesn't support audio recording.");
                    return;
                }

                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

                    mediaRecorder = new MediaRecorder(stream, {
                        mimeType: 'audio/webm'
                    });

                    audioChunks = [];
                    mediaRecorder.ondataavailable = event => {
                        if (event.data.size > 0) {
                            audioChunks.push(event.data);
                        }
                    };

                    mediaRecorder.onstop = () => {
                        const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                        const audioUrl = URL.createObjectURL(audioBlob);

                        audioPlayback.src = audioUrl;
                        audioPlayback.style.display = 'block';

                        // Encode to base64
                        const reader = new FileReader();
                        reader.readAsDataURL(audioBlob);
                        reader.onloadend = function () {
                            hiddenInput.value = reader.result;
                        };
                    };

                    mediaRecorder.start(); // no timeslice, no limit
                    isRecording = true;

                    statusText.style.display = 'block';
                    audioPlayback.style.display = 'none';
                    hiddenInput.value = '';

                    // Change icon
                    recordIcon.classList.remove('fa-microphone');
                    recordIcon.classList.add('fa-stop');
                    recordIcon.classList.remove('text-danger');
                    recordIcon.classList.add('text-dark');

                    // Stop automatically after 30 minutes (safety)
                    setTimeout(() => {
                        if (isRecording) {
                            mediaRecorder.stop();
                            isRecording = false;
                            statusText.style.display = 'none';
                            recordIcon.classList.add('fa-microphone');
                            recordIcon.classList.remove('fa-stop');
                            recordIcon.classList.add('text-danger');
                            recordIcon.classList.remove('text-dark');
                        }
                    }, 1800000); // 30 mins
                } catch (err) {
                    alert("Could not start recording: " + err.message);
                }
            } else {
                mediaRecorder.stop();
                isRecording = false;
                statusText.style.display = 'none';

                recordIcon.classList.add('fa-microphone');
                recordIcon.classList.remove('fa-stop');
                recordIcon.classList.add('text-danger');
                recordIcon.classList.remove('text-dark');
            }
        });
    </script>
    <script>
        const subcategories = {
            civil: [
                'Property Disputes',
                'Contract Disputes',
                'Personal Injury',
                'Financial Disputes',
                'Family Law',
                'Administrative Suits'
            ],
            criminal: [
                'Crimes Against Humanity',
                'Crimes Against Property',
                'Crimes Against Persons',
                'Crimes Related to Narcotics',
                'Other Crimes'
            ]
        };

        function populateSubcategories(category, selected = null) {
            const subcategorySelect = document.getElementById('subcategory');
            subcategorySelect.innerHTML = '<option value="">-- Select Subcategory --</option>';

            if (subcategories[category]) {
                subcategories[category].forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub;
                    opt.text = sub;
                    if (sub === selected) opt.selected = true;
                    subcategorySelect.appendChild(opt);
                });
            }
        }

        document.getElementById('category').addEventListener('change', function () {
            populateSubcategories(this.value);
        });

        // Auto-fill on page load if old values exist
        document.addEventListener('DOMContentLoaded', function () {
            const selectedCategory = "{{ old('category') }}";
            const selectedSub = "{{ old('subcategory') }}";
            if (selectedCategory) {
                populateSubcategories(selectedCategory, selectedSub);
            }
        });
    </script>
@stop
