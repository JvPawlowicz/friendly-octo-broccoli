/**
 * Integração Tiptap com Livewire
 * Usado no componente FormEvolucao.php
 */

import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';

let editorInstances = new Map();

export function initTiptapEditor(elementId, initialContent = '', placeholder = 'Digite aqui...', livewireComponent = null, fieldName = null) {
    const element = document.getElementById(elementId);
    
    if (!element) {
        return null;
    }

    // Destruir instância anterior se existir
    if (editorInstances.has(elementId)) {
        editorInstances.get(elementId).destroy();
    }

    // Criar container para toolbar e editor
    const container = element.parentElement;
    const toolbarId = elementId + '-toolbar';
    
    // Criar toolbar se não existir
    let toolbar = document.getElementById(toolbarId);
    if (!toolbar) {
        toolbar = document.createElement('div');
        toolbar.id = toolbarId;
        toolbar.className = 'flex items-center gap-1 p-2 border-b border-gray-200 bg-gray-50 rounded-t-md';
        container.insertBefore(toolbar, element);
    }

    const editor = new Editor({
        element: element,
        extensions: [
            StarterKit.configure({
                heading: {
                    levels: [1, 2, 3],
                },
            }),
            Placeholder.configure({
                placeholder: placeholder,
            }),
        ],
        content: initialContent,
        editorProps: {
            attributes: {
                class: 'prose prose-sm max-w-none focus:outline-none min-h-[200px] p-4',
            },
        },
        onUpdate: ({ editor }) => {
            const html = editor.getHTML();
            
            // Atualizar textarea oculto
            const textarea = document.getElementById(fieldName || elementId.replace('editor-', ''));
            if (textarea) {
                textarea.value = html;
            }
            
            // Atualizar Livewire
            if (livewireComponent && fieldName) {
                livewireComponent.set(fieldName, html);
            } else if (window.Livewire) {
                const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                if (wireId) {
                    const component = window.Livewire.find(wireId);
                    if (component && fieldName) {
                        component.set(fieldName, html);
                    }
                }
            }
        },
        onBlur: () => {
            // Autosave ao perder foco
            if (window.Livewire) {
                const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                if (wireId) {
                    window.Livewire.find(wireId)?.call('autosave');
                }
            }
        },
    });

    // Criar botões da toolbar
    const createToolbarButton = (icon, onClick, isActive = false, title = '') => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = `p-2 rounded hover:bg-gray-200 transition-colors ${isActive ? 'bg-gray-300' : ''}`;
        button.title = title;
        button.innerHTML = icon;
        button.addEventListener('click', onClick);
        return button;
    };

    // Limpar toolbar existente
    toolbar.innerHTML = '';

    // Botões da toolbar
    toolbar.appendChild(createToolbarButton(
        '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M15.707 4.293a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-5-5a1 1 0 011.414-1.414L10 8.586l4.293-4.293a1 1 0 011.414 0z"/></svg>',
        () => editor.chain().focus().undo().run(),
        false,
        'Desfazer (Ctrl+Z)'
    ));

    toolbar.appendChild(createToolbarButton(
        '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0l5 5a1 1 0 01-1.414 1.414L10 6.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5z"/></svg>',
        () => editor.chain().focus().redo().run(),
        false,
        'Refazer (Ctrl+Y)'
    ));

    // Separador
    const separator1 = document.createElement('div');
    separator1.className = 'w-px h-6 bg-gray-300 mx-1';
    toolbar.appendChild(separator1);

    toolbar.appendChild(createToolbarButton(
        '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>',
        () => editor.chain().focus().toggleBold().run(),
        editor.isActive('bold'),
        'Negrito (Ctrl+B)'
    ));

    toolbar.appendChild(createToolbarButton(
        '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M8 4v12m4-12v12M4 8h12M4 12h12"/></svg>',
        () => editor.chain().focus().toggleItalic().run(),
        editor.isActive('italic'),
        'Itálico (Ctrl+I)'
    ));

    // Separador
    const separator2 = document.createElement('div');
    separator2.className = 'w-px h-6 bg-gray-300 mx-1';
    toolbar.appendChild(separator2);

    toolbar.appendChild(createToolbarButton(
        '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm-1 4a1 1 0 011-1h12a1 1 0 110 2H3a1 1 0 01-1-1zm2 4a1 1 0 100-2h10a1 1 0 100 2H4z" clip-rule="evenodd"/></svg>',
        () => editor.chain().focus().toggleBulletList().run(),
        editor.isActive('bulletList'),
        'Lista com marcadores'
    ));

    toolbar.appendChild(createToolbarButton(
        '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>',
        () => editor.chain().focus().toggleOrderedList().run(),
        editor.isActive('orderedList'),
        'Lista numerada'
    ));

    // Atualizar estado dos botões quando o editor muda
    const updateToolbarButtons = () => {
        const buttons = toolbar.querySelectorAll('button');
        let buttonIndex = 0;
        
        buttons.forEach((btn) => {
            const title = btn.title || '';
            
            if (title.includes('Negrito')) {
                const isActive = editor.isActive('bold');
                btn.className = btn.className.replace(/bg-gray-\d+/g, '') + (isActive ? ' bg-gray-300' : '');
            } else if (title.includes('Itálico')) {
                const isActive = editor.isActive('italic');
                btn.className = btn.className.replace(/bg-gray-\d+/g, '') + (isActive ? ' bg-gray-300' : '');
            } else if (title.includes('marcadores')) {
                const isActive = editor.isActive('bulletList');
                btn.className = btn.className.replace(/bg-gray-\d+/g, '') + (isActive ? ' bg-gray-300' : '');
            } else if (title.includes('numerada')) {
                const isActive = editor.isActive('orderedList');
                btn.className = btn.className.replace(/bg-gray-\d+/g, '') + (isActive ? ' bg-gray-300' : '');
            }
        });
    };

    editor.on('selectionUpdate', updateToolbarButtons);
    editor.on('transaction', updateToolbarButtons);

    editorInstances.set(elementId, editor);
    return editor;
}

// Helper para métodos do editor
export function getEditor(elementId) {
    return editorInstances.get(elementId);
}

// Métodos auxiliares
export const editorActions = {
    bold: (elementId) => {
        const editor = getEditor(elementId);
        if (editor) editor.chain().focus().toggleBold().run();
    },
    italic: (elementId) => {
        const editor = getEditor(elementId);
        if (editor) editor.chain().focus().toggleItalic().run();
    },
    heading: (elementId, level) => {
        const editor = getEditor(elementId);
        if (editor) editor.chain().focus().toggleHeading({ level }).run();
    },
    bulletList: (elementId) => {
        const editor = getEditor(elementId);
        if (editor) editor.chain().focus().toggleBulletList().run();
    },
    orderedList: (elementId) => {
        const editor = getEditor(elementId);
        if (editor) editor.chain().focus().toggleOrderedList().run();
    },
    undo: (elementId) => {
        const editor = getEditor(elementId);
        if (editor) editor.chain().focus().undo().run();
    },
    redo: (elementId) => {
        const editor = getEditor(elementId);
        if (editor) editor.chain().focus().redo().run();
    },
};

// Exportar para uso global
window.initTiptapEditor = initTiptapEditor;
window.getEditor = getEditor;
window.editorActions = editorActions;

