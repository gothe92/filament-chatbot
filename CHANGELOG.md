# Changelog

All notable changes to `filament-chatbot` will be documented in this file.

## v1.0.0 - 2025-01-09

### Added
- Initial release of Filament Chatbot Plugin
- **Universal Chatbot Support**: Add AI-powered chatbot to any Eloquent model using the `HasChatbot` trait
- **Livewire Integration**: Real-time chat interface with reactive components
- **Customizable Views**: Separate JavaScript and Blade templates for maximum flexibility
- **Multiple RAG Modes**: 
  - Documents Only: Strict document-based responses
  - Documents + AI: Hybrid approach using documents and AI knowledge
  - AI Only: Pure AI responses without documents
  - All Documents: Use all available documents (high token usage)
- **Document Management**: Upload and process documents with automatic chunking and embedding
- **Predefined Questions**: Set up common questions with pre-written answers
- **Multi-language Support**: Hungarian and English translations included
- **Database Models**: Complete set of models for chatbot resources, conversations, messages, documents
- **Morph Relationships**: Universal chatbot functionality for any model
- **API Endpoints**: RESTful API for chat functionality
- **Configuration**: Extensive configuration options for AI providers, RAG settings, UI customization
- **Security**: Rate limiting and content filtering capabilities
- **Analytics**: Conversation tracking and usage statistics
- **GitHub Actions**: Automated testing and code style fixing
- **Comprehensive Documentation**: Installation guide, usage examples, customization instructions

### Technical Features
- Support for Claude 3 Haiku and OpenAI GPT models
- Embedding-based document search with similarity scoring
- Conversation history management with session support
- Token usage tracking and optimization
- Chunk-based document processing for better context
- Event system for extensibility
- Service layer architecture for clean separation of concerns
- Comprehensive test coverage with Pest PHP

### Requirements
- PHP 8.2 or higher
- Laravel 11.0 or 12.0
- Filament 3.0
- Livewire 3.0