<?php

return [
    // General messages
    'chatbot' => 'Chatbot',
    'assistant' => 'Assistant',
    'you' => 'You',
    'online' => 'Online',
    'typing' => 'Typing...',
    'send' => 'Send',

    // UI messages
    'type_message' => 'Type your message...',
    'start_conversation' => 'Start a conversation',
    'ask_anything' => 'Ask me anything about this item!',
    'clear_conversation' => 'Clear conversation',
    'suggested_questions' => 'Suggested questions',

    // Status messages
    'chatbot_not_available' => 'Chatbot is not available for this item.',
    'chatbot_disabled' => 'Chatbot is currently disabled.',
    'no_documents_found' => 'I don\'t have enough information to answer your question. Please try asking something else.',
    'error_generating_response' => 'Sorry, there was an error generating a response. Please try again.',

    // RAG modes
    'rag_modes' => [
        'documents_only' => 'Documents Only',
        'documents_and_ai' => 'Documents + AI Knowledge',
        'ai_only' => 'AI Knowledge Only',
        'all_documents' => 'All Documents (High Token Usage)',
    ],

    // Features
    'predefined_questions' => 'Quick Questions',
    'conversation_history' => 'Conversation History',
    'document_upload' => 'Upload Documents',
    'export_conversation' => 'Export Conversation',

    // Admin labels
    'admin' => [
        'chatbot_resources' => 'Chatbot Resources',
        'chatbot_resource' => 'Chatbot Resource',
        'conversations' => 'Conversations',
        'conversation' => 'Conversation',
        'documents' => 'Documents',
        'document' => 'Document',
        'predefined_questions' => 'Predefined Questions',
        'predefined_question' => 'Predefined Question',
        'messages' => 'Messages',
        'message' => 'Message',
        'settings' => 'Settings',
        'analytics' => 'Analytics',
        'statistics' => 'Statistics',

        // Form fields
        'title' => 'Title',
        'content' => 'Content',
        'question' => 'Question',
        'answer' => 'Answer',
        'active' => 'Active',
        'order' => 'Order',
        'rag_mode' => 'RAG Mode',
        'metadata' => 'Metadata',
        'tokens_used' => 'Tokens Used',
        'role' => 'Role',
        'session_id' => 'Session ID',
        'language' => 'Language',
        'file_path' => 'File Path',
        'file_type' => 'File Type',
        'chunks_count' => 'Chunks Count',
        'embedding' => 'Embedding',
        'chunk_index' => 'Chunk Index',
        'similarity_score' => 'Similarity Score',
        'response_time' => 'Response Time',
        'user_satisfaction' => 'User Satisfaction',

        // Actions
        'enable_chatbot' => 'Enable Chatbot',
        'disable_chatbot' => 'Disable Chatbot',
        'view_conversations' => 'View Conversations',
        'view_messages' => 'View Messages',
        'upload_document' => 'Upload Document',
        'generate_embeddings' => 'Generate Embeddings',
        'process_document' => 'Process Document',
        'add_question' => 'Add Question',
        'edit_question' => 'Edit Question',
        'reorder_questions' => 'Reorder Questions',
        'export_data' => 'Export Data',
        'clear_history' => 'Clear History',

        // Status
        'enabled' => 'Enabled',
        'disabled' => 'Disabled',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'pending' => 'Pending',

        // Descriptions
        'chatbot_resource_description' => 'Configure chatbot settings for this resource.',
        'rag_mode_description' => 'Choose how the chatbot should generate responses.',
        'documents_description' => 'Upload and manage documents for the chatbot to reference.',
        'predefined_questions_description' => 'Create pre-written questions and answers for quick responses.',
        'conversation_description' => 'View and manage conversations between users and the chatbot.',

        // Help text
        'rag_mode_help' => 'Documents Only: Uses only uploaded documents. Documents + AI: Prefers documents but can use AI knowledge. AI Only: Ignores documents. All Documents: Uses all documents (expensive).',
        'embedding_help' => 'Embeddings are used for semantic search in documents.',
        'chunk_help' => 'Large documents are split into smaller chunks for better processing.',
        'temperature_help' => 'Lower values make responses more focused, higher values more creative.',
    ],

    // Validation messages
    'validation' => [
        'message_required' => 'Message is required.',
        'message_max_length' => 'Message is too long.',
        'title_required' => 'Title is required.',
        'content_required' => 'Content is required.',
        'question_required' => 'Question is required.',
        'answer_required' => 'Answer is required.',
        'file_required' => 'File is required.',
        'file_max_size' => 'File is too large.',
        'file_invalid_type' => 'Invalid file type.',
    ],

    // Success messages
    'success' => [
        'chatbot_enabled' => 'Chatbot has been enabled.',
        'chatbot_disabled' => 'Chatbot has been disabled.',
        'document_uploaded' => 'Document has been uploaded successfully.',
        'document_processed' => 'Document has been processed successfully.',
        'embeddings_generated' => 'Embeddings have been generated successfully.',
        'question_added' => 'Question has been added successfully.',
        'question_updated' => 'Question has been updated successfully.',
        'question_deleted' => 'Question has been deleted successfully.',
        'conversation_cleared' => 'Conversation has been cleared.',
        'settings_updated' => 'Settings have been updated successfully.',
    ],

    // Error messages
    'errors' => [
        'chatbot_not_found' => 'Chatbot resource not found.',
        'document_not_found' => 'Document not found.',
        'conversation_not_found' => 'Conversation not found.',
        'question_not_found' => 'Question not found.',
        'file_upload_failed' => 'File upload failed.',
        'processing_failed' => 'Processing failed.',
        'embedding_generation_failed' => 'Embedding generation failed.',
        'ai_service_unavailable' => 'AI service is currently unavailable.',
        'rate_limit_exceeded' => 'Rate limit exceeded. Please wait before sending another message.',
        'session_expired' => 'Session has expired. Please refresh the page.',
    ],
];
