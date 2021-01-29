@php($files = is_array($files) ? json_encode($files) : $files)
<file-uploader :media="{{ $files ?? '[]' }}"
               {{ $max ? ':max="'.$max.'"' : '' }}
               {{ $form ? 'form="'.$form.'"' : '' }}
               {{ $unlimited ? ':unlimited="true"' : '' }}
               collection="{{ $collection }}"
               :tokens="{{ json_encode(old('media', [])) }}"
               label="{{ $label }}"
               notes="{{ $notes }}"
               accept="audio/*"
></file-uploader>
