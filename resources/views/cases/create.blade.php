@extends('adminlte::page')

@section('title', 'Create Issue')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Create Issue</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cases.index') }}">Issues</a></li>
                <li class="breadcrumb-item active">Create Issue</li>
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
                                <div class="form-group col-md-3">
                                    <label for="country">Country</label>
                                    <input type="text" name="country" id="country" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="division">Division</label>
                                    <input type="text" name="division" id="division" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="district">District</label>
                                    <input type="text" name="district" id="district" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="thana">Police Station</label>
                                    <input type="text" name="thana" id="thana" class="form-control">
                                </div>

                                @if(auth()->user()->hasRole('admin'))
                                    <div class="form-group col-md-6">
                                        <label for="client_id">Client</label>
                                        @if ($clients->isNotEmpty())
                                            <select name="client_id" class="form-control select2">
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                        {{ $client->user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select name="client_id" id="clientSelect" class="form-control select2"></select>
                                        @endif
                                    </div>
                                @elseif(auth()->user()->hasRole('client'))
                                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                                @endif

                                {{-- Category --}}
                                <div class="form-group col-md-6">
                                    <label for="category">Issue Category</label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="civil" {{ old('category') == 'civil' ? 'selected' : '' }}>Civil</option>
                                        <option value="criminal" {{ old('category') == 'criminal' ? 'selected' : '' }}>Criminal</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <label for="description">Issue's Description</label>
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

                            <div class="form-group">
                                <label for="uploaded_file">Upload File (jpg, png, mp3, mp4, max 100MB)</label>
                                <input type="file" name="uploaded_file" id="uploaded_file" class="form-control" accept=".jpg,.jpeg,.png,.mp3,.mp4" onchange="validateFileSize(this)">
                            </div>

                            @can('cases.create')
                                <button type="submit" class="btn btn-success">Create Issue</button>
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

    <style>
        #recorder-container button {
            width: 80px;
            height: 80px;
        }

        #recordBtn:hover {
            background-color: #f5f5f5;
        }
    </style>
@stop

@section('js')
    <script>
        function validateFileSize(input) {
            if (input.files[0].size > 100 * 1024 * 1024) {
                alert("File size must be less than 100MB.");
                input.value = ""; // clear the input
            }
        }
    </script>

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
@stop

