@props(['name', 'value' => ''])

@once
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <style>
        .ql-toolbar.ql-snow {
            border: 1px solid var(--border);
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            background-color: var(--surface-hover);
            font-family: 'Inter', sans-serif;
            position: sticky;
            top: 57px; /* Matches nav height */
            z-index: 100;
        }
        .ql-container.ql-snow {
            border: 1px solid var(--border);
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            border-top: none;
            background-color: var(--input-bg);
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            color: var(--text);
            min-height: 250px;
        }
        .ql-editor {
            min-height: 250px;
            color: var(--text);
        }
        .ql-editor p {
            margin-bottom: 0.5em;
            color: inherit;
        }
        .ql-editor blockquote {
            border-left: 4px solid var(--primary);
            padding-left: 16px;
            margin-left: 0;
            font-style: italic;
            color: var(--text-secondary);
            background: var(--surface-hover);
            padding: 12px 16px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 1em;
        }
        .ql-snow .ql-stroke {
            stroke: var(--text-secondary);
        }
        .ql-snow .ql-fill, .ql-snow .ql-stroke.ql-fill {
            fill: var(--text-secondary);
        }
        .ql-snow .ql-picker {
            color: var(--text-secondary);
        }
        .ql-snow.ql-toolbar button:hover .ql-stroke, 
        .ql-snow .ql-toolbar button:hover .ql-stroke, 
        .ql-snow.ql-toolbar button:focus .ql-stroke, 
        .ql-snow .ql-toolbar button:focus .ql-stroke, 
        .ql-snow.ql-toolbar button.ql-active .ql-stroke, 
        .ql-snow .ql-toolbar button.ql-active .ql-stroke, 
        .ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke, 
        .ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke, 
        .ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-stroke, 
        .ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-stroke, 
        .ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke, 
        .ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke, 
        .ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-stroke, 
        .ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-stroke, 
        .ql-snow.ql-toolbar button:hover .ql-stroke-miter, 
        .ql-snow .ql-toolbar button:hover .ql-stroke-miter, 
        .ql-snow.ql-toolbar button:focus .ql-stroke-miter, 
        .ql-snow .ql-toolbar button:focus .ql-stroke-miter, 
        .ql-snow.ql-toolbar button.ql-active .ql-stroke-miter, 
        .ql-snow .ql-toolbar button.ql-active .ql-stroke-miter, 
        .ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke-miter, 
        .ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke-miter, 
        .ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-stroke-miter, 
        .ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-stroke-miter, 
        .ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke-miter, 
        .ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke-miter, 
        .ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-stroke-miter, 
        .ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-stroke-miter {
            stroke: var(--primary);
        }
        .ql-snow.ql-toolbar button:hover .ql-fill, 
        .ql-snow .ql-toolbar button:hover .ql-fill, 
        .ql-snow.ql-toolbar button:focus .ql-fill, 
        .ql-snow .ql-toolbar button:focus .ql-fill, 
        .ql-snow.ql-toolbar button.ql-active .ql-fill, 
        .ql-snow .ql-toolbar button.ql-active .ql-fill, 
        .ql-snow.ql-toolbar .ql-picker-label:hover .ql-fill, 
        .ql-snow .ql-toolbar .ql-picker-label:hover .ql-fill, 
        .ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-fill, 
        .ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-fill, 
        .ql-snow.ql-toolbar .ql-picker-item:hover .ql-fill, 
        .ql-snow .ql-toolbar .ql-picker-item:hover .ql-fill, 
        .ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-fill, 
        .ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-fill, 
        .ql-snow.ql-toolbar button:hover .ql-stroke.ql-fill, 
        .ql-snow .ql-toolbar button:hover .ql-stroke.ql-fill, 
        .ql-snow.ql-toolbar button:focus .ql-stroke.ql-fill, 
        .ql-snow .ql-toolbar button:focus .ql-stroke.ql-fill, 
        .ql-snow.ql-toolbar button.ql-active .ql-stroke.ql-fill, 
        .ql-snow .ql-toolbar button.ql-active .ql-stroke.ql-fill, 
        .ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke.ql-fill, 
        .ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke.ql-fill, 
        .ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-stroke.ql-fill, 
        .ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-stroke.ql-fill, 
        .ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke.ql-fill, 
        .ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke.ql-fill, 
        .ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-stroke.ql-fill, 
        .ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-stroke.ql-fill {
            fill: var(--primary);
        }
        .ql-snow.ql-toolbar .ql-picker-label:hover, 
        .ql-snow .ql-toolbar .ql-picker-label:hover, 
        .ql-snow.ql-toolbar .ql-picker-label.ql-active, 
        .ql-snow .ql-toolbar .ql-picker-label.ql-active, 
        .ql-snow.ql-toolbar .ql-picker-item:hover, 
        .ql-snow .ql-toolbar .ql-picker-item:hover, 
        .ql-snow.ql-toolbar .ql-picker-item.ql-selected, 
        .ql-snow .ql-toolbar .ql-picker-item.ql-selected {
            color: var(--primary);
        }
        .ql-snow .ql-tooltip {
            border-radius: 8px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            background-color: var(--surface);
            color: var(--text);
        }
        .ql-snow .ql-tooltip input[type=text] {
            border: 1px solid var(--border);
            border-radius: 4px;
            background-color: var(--input-bg);
            color: var(--text);
            height: 32px;
            padding: 0 8px;
        }
        
        /* Custom Animated Tooltips */
        .ql-toolbar button, .ql-toolbar .ql-picker {
            position: relative;
        }
        
        .ql-toolbar button::after, .ql-toolbar .ql-picker::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(4px);
            background-color: var(--text);
            color: var(--surface);
            padding: 5px 9px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: var(--shadow-md);
            z-index: 100;
        }

        .ql-toolbar button:hover::after, .ql-toolbar .ql-picker:hover::after {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(-6px);
        }
    </style>
@endonce

@php
    $id = 'wysiwyg-' . Str::random(8);
@endphp

<div style="margin-top: 4px; font-weight: normal;">
    <!-- Hidden input to hold the actual value for form submission -->
    <input type="hidden" name="{{ $name }}" id="{{ $id }}-input" value="{{ $value }}">

    <!-- Editor container -->
    <div id="{{ $id }}-editor">{!! $value !!}</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var quill = new Quill('#{{ $id }}-editor', {
            theme: 'snow',
            bounds: '#{{ $id }}-editor',
            placeholder: 'Write your content here...',
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [2, 3, 4, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'script': 'sub'}, { 'script': 'super' }],
                        [
                            { 'color': [false, '#000000', '#111827', '#374151', '#6b7280', '#9ca3af', '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#22c55e', '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#d946ef', '#ec4899'] }, 
                            { 'background': [false, '#000000', '#111827', '#374151', '#6b7280', '#9ca3af', '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#22c55e', '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#d946ef', '#ec4899', '#f3f4f6', '#ffffff', '#fef2f2', '#fff7ed', '#f0fdf4', '#eff6ff'] }
                        ],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            }
        });

        var inputElement = document.getElementById('{{ $id }}-input');
        
        quill.on('text-change', function() {
            var html = quill.root.innerHTML;
            if (html === '<p><br></p>') {
                html = '';
            }
            inputElement.value = html;
        });

        // Add custom tooltips
        setTimeout(function() {
            var tooltips = {
                '.ql-bold': 'Bold',
                '.ql-italic': 'Italic',
                '.ql-underline': 'Underline',
                '.ql-strike': 'Strikethrough',
                '.ql-script[value="sub"]': 'Subscript',
                '.ql-script[value="super"]': 'Superscript',
                '.ql-color': 'Text Color',
                '.ql-background': 'Background Color',
                '.ql-blockquote': 'Quote',
                '.ql-code-block': 'Code Block',
                '.ql-list[value="ordered"]': 'Numbered List',
                '.ql-list[value="bullet"]': 'Bullet List',
                '.ql-link': 'Insert Link',
                '.ql-image': 'Insert Image',
                '.ql-video': 'Insert Video',
                '.ql-clean': 'Clear Formatting',
                '.ql-header': 'Heading Format',
                '.ql-align': 'Alignment'
            };

            var editorEl = document.getElementById('{{ $id }}-editor');
            if (editorEl && editorEl.previousElementSibling) {
                var toolbar = editorEl.previousElementSibling;
                for (var selector in tooltips) {
                    var elements = toolbar.querySelectorAll(selector);
                    elements.forEach(function(el) {
                        el.setAttribute('data-tooltip', tooltips[selector]);
                    });
                }
            }
        }, 100);
    });
</script>
