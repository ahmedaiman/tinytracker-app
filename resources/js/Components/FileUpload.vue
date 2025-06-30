<script setup>
import { ref, computed, onMounted, watch, toRefs } from 'vue';

const props = defineProps({
  modelValue: {
    type: [File, Array],
    default: null,
  },
  id: {
    type: String,
    default: '',
  },
  name: {
    type: String,
    default: 'file',
  },
  label: {
    type: String,
    default: 'Choose a file',
  },
  placeholder: {
    type: String,
    default: 'No file chosen',
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  accept: {
    type: String,
    default: '*/*',
  },
  maxSize: {
    type: [Number, String],
    default: 10, // MB
  },
  maxFiles: {
    type: [Number, String],
    default: 1,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
  error: {
    type: [String, Boolean],
    default: '',
  },
  help: {
    type: String,
    default: '',
  },
  preview: {
    type: Boolean,
    default: true,
  },
  previewWidth: {
    type: [String, Number],
    default: '100%',
  },
  previewHeight: {
    type: [String, Number],
    default: 'auto',
  },
  previewFit: {
    type: String,
    default: 'cover',
    validator: (value) => ['cover', 'contain', 'fill', 'none', 'scale-down'].includes(value),
  },
  color: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'accent', 'success', 'warning', 'error', 'info'].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'drag-drop', 'button', 'avatar'].includes(value),
  },
  icon: {
    type: String,
    default: 'upload',
  },
  iconSize: {
    type: [String, Number],
    default: 24,
  },
  showFileList: {
    type: Boolean,
    default: true,
  },
  showSize: {
    type: Boolean,
    default: true,
  },
  showRemove: {
    type: Boolean,
    default: true,
  },
  showPreview: {
    type: Boolean,
    default: true,
  },
  removable: {
    type: Boolean,
    default: true,
  },
  disabledTypes: {
    type: Array,
    default: () => ['application/x-msdownload', 'application/x-msdos-program'],
  },
});

const emit = defineEmits([
  'update:modelValue',
  'change',
  'remove',
  'error',
  'exceed',
  'preview',
  'progress',
]);

const { 
  modelValue, 
  multiple, 
  maxFiles, 
  maxSize, 
  accept, 
  disabled, 
  preview, 
  color, 
  size, 
  variant,
  disabledTypes,
} = toRefs(props);

const fileInput = ref(null);
const dropZone = ref(null);
const isDragging = ref(false);
const files = ref([]);
const previews = ref({});
const uploadProgress = ref({});
const errorMessage = ref('');

// Size classes
const sizeClasses = computed(() => ({
  sm: {
    button: 'px-2.5 py-1.5 text-xs',
    preview: 'text-xs',
    icon: 'h-4 w-4',
  },
  md: {
    button: 'px-3 py-2 text-sm',
    preview: 'text-sm',
    icon: 'h-5 w-5',
  },
  lg: {
    button: 'px-4 py-3 text-base',
    preview: 'text-base',
    icon: 'h-6 w-6',
  },
}));

// Color classes with new Tailwind tokens
const colorClasses = computed(() => ({
  primary: {
    button: 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-100 hover:bg-primary-200 dark:hover:bg-primary-800 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400',
    border: 'border-primary-200 dark:border-primary-700',
    text: 'text-primary-700 dark:text-primary-200',
    hover: 'hover:bg-primary-50 dark:hover:bg-primary-900/50',
    active: 'active:bg-primary-100 dark:active:bg-primary-800',
    focus: 'focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400',
    icon: 'text-primary-500 dark:text-primary-400',
  },
  secondary: {
    button: 'bg-secondary-100 dark:bg-secondary-900 text-secondary-700 dark:text-secondary-100 hover:bg-secondary-200 dark:hover:bg-secondary-800 focus:ring-2 focus:ring-secondary-500 dark:focus:ring-secondary-400',
    border: 'border-secondary-200 dark:border-secondary-700',
    text: 'text-secondary-700 dark:text-secondary-200',
    hover: 'hover:bg-secondary-50 dark:hover:bg-secondary-900/50',
    active: 'active:bg-secondary-100 dark:active:bg-secondary-800',
    focus: 'focus:ring-2 focus:ring-secondary-500 dark:focus:ring-secondary-400',
    icon: 'text-secondary-500 dark:text-secondary-400',
  },
  accent: {
    button: 'bg-accent-100 dark:bg-accent-900 text-accent-700 dark:text-accent-100 hover:bg-accent-200 dark:hover:bg-accent-800 focus:ring-2 focus:ring-accent-500 dark:focus:ring-accent-400',
    border: 'border-accent-200 dark:border-accent-700',
    text: 'text-accent-700 dark:text-accent-200',
    hover: 'hover:bg-accent-50 dark:hover:bg-accent-900/50',
    active: 'active:bg-accent-100 dark:active:bg-accent-800',
    focus: 'focus:ring-2 focus:ring-accent-500 dark:focus:ring-accent-400',
    icon: 'text-accent-500 dark:text-accent-400',
  },
  success: {
    button: 'bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-100 hover:bg-success-200 dark:hover:bg-success-800/50 focus:ring-2 focus:ring-success-500 dark:focus:ring-success-400',
    border: 'border-success-200 dark:border-success-800',
    text: 'text-success-700 dark:text-success-300',
    hover: 'hover:bg-success-50 dark:hover:bg-success-900/30',
    active: 'active:bg-success-100 dark:active:bg-success-900/40',
    focus: 'focus:ring-2 focus:ring-success-500 dark:focus:ring-success-400',
    icon: 'text-success-500 dark:text-success-400',
  },
  warning: {
    button: 'bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-100 hover:bg-warning-200 dark:hover:bg-warning-800/50 focus:ring-2 focus:ring-warning-500 dark:focus:ring-warning-400',
    border: 'border-warning-200 dark:border-warning-800',
    text: 'text-warning-700 dark:text-warning-200',
    hover: 'hover:bg-warning-50 dark:hover:bg-warning-900/30',
    active: 'active:bg-warning-100 dark:active:bg-warning-900/40',
    focus: 'focus:ring-2 focus:ring-warning-500 dark:focus:ring-warning-400',
    icon: 'text-warning-500 dark:text-warning-400',
  },
  error: {
    button: 'bg-error-100 dark:bg-error-900/30 text-error-700 dark:text-error-100 hover:bg-error-200 dark:hover:bg-error-800/50 focus:ring-2 focus:ring-error-500 dark:focus:ring-error-400',
    border: 'border-error-200 dark:border-error-800',
    text: 'text-error-700 dark:text-error-200',
    hover: 'hover:bg-error-50 dark:hover:bg-error-900/30',
    active: 'active:bg-error-100 dark:active:bg-error-900/40',
    focus: 'focus:ring-2 focus:ring-error-500 dark:focus:ring-error-400',
    icon: 'text-error-500 dark:text-error-400',
  },
  info: {
    button: 'bg-info-100 dark:bg-info-900/30 text-info-700 dark:text-info-100 hover:bg-info-200 dark:hover:bg-info-800/50 focus:ring-2 focus:ring-info-500 dark:focus:ring-info-400',
    border: 'border-info-200 dark:border-info-800',
    text: 'text-info-700 dark:text-info-200',
    hover: 'hover:bg-info-50 dark:hover:bg-info-900/30',
    active: 'active:bg-info-100 dark:active:bg-info-900/40',
    focus: 'focus:ring-2 focus:ring-info-500 dark:focus:ring-info-400',
    icon: 'text-info-500 dark:text-info-400',
  },
}));

// Format file size
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Get file icon based on type
const getFileIcon = (file) => {
  const type = file.type.split('/')[0];
  const extension = file.name.split('.').pop().toLowerCase();
  
  const icons = {
    image: 'image',
    audio: 'music-note',
    video: 'video-camera',
    application: 'document',
    text: 'document-text',
  };
  
  const extensionIcons = {
    pdf: 'document-pdf',
    doc: 'document-text',
    docx: 'document-text',
    xls: 'document-spreadsheet',
    xlsx: 'document-spreadsheet',
    ppt: 'document-presentation',
    pptx: 'document-presentation',
    zip: 'folder-archive',
    rar: 'folder-archive',
    gz: 'folder-archive',
    exe: 'chip',
    mp3: 'music-note',
    wav: 'music-note',
    mp4: 'video-camera',
    avi: 'video-camera',
    jpg: 'photograph',
    jpeg: 'photograph',
    png: 'photograph',
    gif: 'photograph',
    svg: 'photograph',
    txt: 'document-text',
    csv: 'document-spreadsheet',
  };
  
  return extensionIcons[extension] || icons[type] || 'document';
};

// Generate preview for image files
const generatePreview = (file) => {
  return new Promise((resolve) => {
    const reader = new FileReader();
    
    reader.onload = (e) => {
      previews.value[file.name] = e.target.result;
      resolve(e.target.result);
    };
    
    if (file.type.startsWith('image/')) {
      reader.readAsDataURL(file);
    } else {
      previews.value[file.name] = null;
      resolve(null);
    }
  });
};

// Handle file selection
const handleFileSelect = async (event) => {
  errorMessage.value = '';
  const selectedFiles = Array.from(event.target.files || event.dataTransfer?.files || []);
  
  if (selectedFiles.length === 0) return;
  
  // Check max files
  const maxFilesCount = parseInt(maxFiles.value);
  if (maxFilesCount > 0 && selectedFiles.length > maxFilesCount) {
    errorMessage.value = `You can only upload up to ${maxFilesCount} files at a time.`;
    emit('exceed', { files: selectedFiles, message: errorMessage.value });
    return;
  }
  
  // Process each file
  const validFiles = [];
  const maxSizeBytes = parseInt(maxSize.value) * 1024 * 1024; // Convert MB to bytes
  
  for (const file of selectedFiles) {
    // Check file size
    if (file.size > maxSizeBytes) {
      errorMessage.value = `File "${file.name}" exceeds the maximum size of ${maxSize.value}MB.`;
      emit('error', { file, error: 'SIZE_EXCEEDED', message: errorMessage.value });
      continue;
    }
    
    // Check file type
    if (disabledTypes.value.includes(file.type)) {
      errorMessage.value = `File type "${file.type}" is not allowed.`;
      emit('error', { file, error: 'TYPE_NOT_ALLOWED', message: errorMessage.value });
      continue;
    }
    
    // Add file to valid files
    validFiles.push(file);
    
    // Generate preview if enabled
    if (preview.value) {
      await generatePreview(file);
    }
  }
  
  if (validFiles.length === 0) return;
  
  // Update model value
  let newValue;
  if (multiple.value) {
    newValue = [...(Array.isArray(modelValue.value) ? modelValue.value : []), ...validFiles];
  } else {
    newValue = validFiles[0];
  }
  
  emit('update:modelValue', newValue);
  emit('change', newValue);
  
  // Reset file input to allow selecting the same file again
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

// Remove file
const removeFile = (index, event) => {
  if (event) event.stopPropagation();
  
  if (Array.isArray(modelValue.value)) {
    const newFiles = [...modelValue.value];
    const removedFile = newFiles.splice(index, 1)[0];
    emit('update:modelValue', newFiles);
    emit('remove', removedFile);
  } else {
    const removedFile = modelValue.value;
    emit('update:modelValue', null);
    emit('remove', removedFile);
  }
};

// Handle drag events
const handleDragOver = (event) => {
  event.preventDefault();
  event.stopPropagation();
  isDragging.value = true;
};

const handleDragLeave = (event) => {
  event.preventDefault();
  event.stopPropagation();
  isDragging.value = false;
};

const handleDrop = (event) => {
  event.preventDefault();
  event.stopPropagation();
  isDragging.value = false;
  
  if (disabled.value) return;
  
  handleFileSelect(event);
};

// Handle preview click
const handlePreview = (file, index) => {
  if (disabled.value) return;
  emit('preview', { file, index, url: previews.value[file.name] });
};

// Watch for model value changes
watch(() => modelValue.value, (newValue) => {
  if (!newValue) {
    files.value = [];
    previews.value = {};
  } else if (Array.isArray(newValue)) {
    files.value = [...newValue];
    newValue.forEach(file => {
      if (file && !previews.value[file.name]) {
        generatePreview(file);
      }
    });
  } else if (newValue && !previews.value[newValue.name]) {
    files.value = [newValue];
    generatePreview(newValue);
  }
}, { immediate: true });

// Expose methods
defineExpose({
  triggerFileSelect: () => fileInput.value?.click(),
  clearFiles: () => {
    emit('update:modelValue', multiple.value ? [] : null);
    emit('change', multiple.value ? [] : null);
  },
  upload: async (url, options = {}) => {
    if (!modelValue.value) return [];
    
    const filesToUpload = Array.isArray(modelValue.value) 
      ? modelValue.value 
      : [modelValue.value];
    
    const results = [];
    
    for (let i = 0; i < filesToUpload.length; i++) {
      const file = filesToUpload[i];
      const formData = new FormData();
      formData.append(options.fieldName || 'file', file);
      
      // Add additional data if provided
      if (options.data) {
        Object.entries(options.data).forEach(([key, value]) => {
          formData.append(key, value);
        });
      }
      
      try {
        const xhr = new XMLHttpRequest();
        
        // Upload progress
        if (xhr.upload) {
          xhr.upload.onprogress = (event) => {
            if (event.lengthComputable) {
              const progress = Math.round((event.loaded / event.total) * 100);
              uploadProgress.value[file.name] = progress;
              emit('progress', { file, progress, event });
            }
          };
        }
        
        // Handle response
        const response = await new Promise((resolve, reject) => {
          xhr.open(options.method || 'POST', url, true);
          
          // Set headers
          if (options.headers) {
            Object.entries(options.headers).forEach(([key, value]) => {
              xhr.setRequestHeader(key, value);
            });
          }
          
          xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) {
              resolve({
                success: true,
                data: xhr.response,
                status: xhr.status,
                file,
              });
            } else {
              reject({
                success: false,
                status: xhr.status,
                statusText: xhr.statusText,
                file,
              });
            }
          };
          
          xhr.onerror = () => {
            reject({
              success: false,
              status: xhr.status,
              statusText: xhr.statusText,
              file,
            });
          };
          
          xhr.send(formData);
        });
        
        results.push(response);
      } catch (error) {
        console.error('Upload error:', error);
        results.push({
          success: false,
          error,
          file,
        });
        
        emit('error', { error, file });
      }
    }
    
    return results;
  },
});
</script>

<template>
  <div class="w-full">
    <!-- Label -->
    <label 
      v-if="label" 
      :for="id"
      class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1"
    >
      {{ label }}
      <span v-if="required" class="text-error-500 dark:text-error-400 ml-0.5">*</span>
    </label>
    
    <!-- Drag and drop zone -->
    <div
      v-if="variant === 'drag-drop'"
      ref="dropZone"
      :class="[
        'border-2 border-dashed rounded-lg p-6 text-center transition-colors',
        'focus:outline-none focus:ring-2 focus:ring-offset-2',
        colorClasses[color].border,
        colorClasses[color].focus,
        {
          'border-primary-300 bg-primary-50 dark:bg-primary-900/30': isDragging && !disabled,
          'border-error-300 dark:border-error-500': error,
          'opacity-50 cursor-not-allowed': disabled,
          'cursor-pointer': !disabled,
        },
      ]"
      @dragover="handleDragOver"
      @dragleave="handleDragLeave"
      @drop="handleDrop"
      @click="!disabled && fileInput?.click()"
      tabindex="0"
      @keydown.enter="!disabled && fileInput?.click()"
    >
      <div class="space-y-2">
        <!-- Icon -->
        <div 
          class="mx-auto flex items-center justify-center h-12 w-12 rounded-full"
          :class="[colorClasses[color].button.replace('hover:bg-', 'bg-').replace(' focus:ring-', ' ')]"
        >
          <svg 
            class="h-6 w-6" 
            :class="colorClasses[color].icon" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" 
            />
          </svg>
        </div>
        
        <!-- Instructions -->
        <div class="space-y-1">
          <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
            <span class="text-primary-600 dark:text-primary-400 hover:underline">Click to upload</span> or drag and drop
          </p>
          <p class="text-xs text-neutral-500 dark:text-neutral-400">
            {{ accept === '*/*' ? 'Any file type' : accept }} up to {{ maxSize }}MB
          </p>
        </div>
      </div>
    </div>
    
    <!-- Button variant -->
    <div v-else-if="variant === 'button'" class="flex items-center space-x-4">
      <button
        type="button"
        :class="[
          'inline-flex items-center rounded-md border border-transparent',
          'shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2',
          sizeClasses[size].button,
          colorClasses[color].button,
          {
            'opacity-50 cursor-not-allowed': disabled,
          },
        ]"
        :disabled="disabled"
        @click="!disabled && fileInput?.click()"
      >
        <svg 
          class="-ml-1 mr-2 h-4 w-4" 
          :class="colorClasses[color].icon" 
          fill="none" 
          viewBox="0 0 24 24" 
          stroke="currentColor"
        >
          <path 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            stroke-width="2" 
            d="M12 4v16m8-8H4" 
          />
        </svg>
        {{ label || 'Choose File' }}
      </button>
      
      <span 
        class="text-sm text-neutral-600 dark:text-neutral-400"
        :class="{
          'opacity-50': disabled,
        }"
      >
        {{ files.length > 0 ? files.map(f => f.name).join(', ') : placeholder }}
      </span>
    </div>
    
    <!-- Default input -->
    <div v-else class="flex rounded-md shadow-sm">
      <input
        :id="id"
        ref="fileInput"
        type="file"
        class="hidden"
        :name="name"
        :multiple="multiple"
        :accept="accept"
        :disabled="disabled"
        @change="handleFileSelect"
      />
      
      <button
        type="button"
        :class="[
          'inline-flex items-center px-4 rounded-l-md border border-r-0',
          'focus:outline-none focus:ring-2 focus:ring-offset-2',
          sizeClasses[size].button,
          colorClasses[color].button,
          {
            'opacity-50 cursor-not-allowed': disabled,
          },
        ]"
        :disabled="disabled"
        @click="!disabled && fileInput?.click()"
      >
        <span>{{ label || 'Choose File' }}</span>
      </button>
      
      <div 
        :class="[
          'flex-1 flex items-center px-3 py-2 border rounded-r-md text-sm leading-5',
          colorClasses[color].border,
          {
            'bg-neutral-50 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300': !disabled,
            'bg-neutral-100 dark:bg-neutral-800/50 text-neutral-400': disabled,
            'border-error-300 dark:border-error-500': error,
          },
        ]"
      >
        {{ files.length > 0 ? files.map(f => f.name).join(', ') : placeholder }}
      </div>
    </div>
    
    <!-- Error message -->
    <p 
      v-if="error || errorMessage" 
      class="mt-1 text-sm text-error-600 dark:text-error-400"
    >
      {{ error || errorMessage }}
    </p>
    
    <!-- Help text -->
    <p 
      v-else-if="help" 
      class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
    >
      {{ help }}
    </p>
    
    <!-- File previews -->
    <div v-if="preview && files.length > 0" class="mt-4 space-y-4">
      <div 
        v-for="(file, index) in files" 
        :key="index"
        class="flex items-center space-x-4 p-3 bg-white dark:bg-gray-800 rounded-lg border"
        :class="colorClasses[color].border"
      >
        <!-- File icon/thumbnail -->
        <div 
          v-if="showPreview"
          class="flex-shrink-0 h-12 w-12 rounded-md overflow-hidden bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center"
          @click="handlePreview(file, index)"
          :class="{ 'cursor-pointer': !disabled }"
        >
          <template v-if="previews[file.name] && file.type.startsWith('image/')">
            <img 
              :src="previews[file.name]" 
              :alt="file.name"
              class="h-full w-full object-cover"
            />
          </template>
          <template v-else>
            <span class="text-neutral-500 dark:text-neutral-400">
              <svg 
                class="h-6 w-6" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  :d="getFileIconPath(getFileIcon(file))"
                />
              </svg>
            </span>
          </template>
        </div>
        
        <!-- File info -->
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100 truncate">
            {{ file.name }}
          </p>
          <div class="flex items-center space-x-2 text-xs text-neutral-500 dark:text-neutral-400">
            <span v-if="showSize">{{ formatFileSize(file.size) }}</span>
            <span class="text-neutral-400 dark:text-neutral-500">•</span>
            <span>{{ file.type || 'Unknown type' }}</span>
            
            <!-- Upload progress -->
            <span 
              v-if="uploadProgress[file.name] !== undefined" 
              class="ml-auto font-medium"
            >
              {{ uploadProgress[file.name] }}%
            </span>
          </div>
        </div>

        <button
          v-if="showRemove && removable && !disabled"
          type="button"
          class="text-neutral-400 hover:text-error-500 dark:hover:text-error-400 focus:outline-none transition-colors duration-200"
          @click.stop="removeFile(index)"
          :title="'Remove ' + file.name"
        >
          <svg 
            class="h-5 w-5" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" 
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>
