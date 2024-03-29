@php($files = is_array($files) ? json_encode($files) : $files)
<file-uploader :media="{{ $files ?? '[]' }}"
               {{ $name ? ':name="'.$name.'"' : '' }}
               {{ $max ? ':max="'.$max.'"' : '' }}
               {{ $form ? 'form="'.$form.'"' : '' }}
               {{ $unlimited ? ':unlimited="true"' : '' }}
               collection="{{ $collection }}"
               :tokens="{{ json_encode(old('media', [])) }}"
               label="{{ $label }}"
               notes="{{ $notes }}"
               max-width="{{ $maxWidth }}"
               max-height="{{ $maxHeight }}"
               accept="image/jpeg,image/png,image/jpg,image/gif"
></file-uploader>
