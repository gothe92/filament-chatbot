<?php

return [
    // Általános üzenetek
    'chatbot' => 'Chatbot',
    'assistant' => 'Asszisztens',
    'you' => 'Te',
    'online' => 'Online',
    'typing' => 'Gépel...',
    'send' => 'Küldés',
    
    // UI üzenetek
    'type_message' => 'Írj egy üzenetet...',
    'start_conversation' => 'Kezdj beszélgetést',
    'ask_anything' => 'Kérdezz bármit erről a tárgyról!',
    'clear_conversation' => 'Beszélgetés törlése',
    'suggested_questions' => 'Javasolt kérdések',
    
    // Állapot üzenetek
    'chatbot_not_available' => 'A chatbot nem elérhető ennél a tárgynál.',
    'chatbot_disabled' => 'A chatbot jelenleg le van tiltva.',
    'no_documents_found' => 'Nincs elég információm a kérdésed megválaszolásához. Próbálj meg mást kérdezni.',
    'error_generating_response' => 'Sajnos hiba történt a válasz generálása során. Kérlek próbáld újra.',
    
    // RAG módok
    'rag_modes' => [
        'documents_only' => 'Csak Dokumentumok',
        'documents_and_ai' => 'Dokumentumok + AI Tudás',
        'ai_only' => 'Csak AI Tudás',
        'all_documents' => 'Minden Dokumentum (Nagy Token Használat)',
    ],
    
    // Funkciók
    'predefined_questions' => 'Gyors Kérdések',
    'conversation_history' => 'Beszélgetés Történet',
    'document_upload' => 'Dokumentum Feltöltés',
    'export_conversation' => 'Beszélgetés Exportálása',
    
    // Admin címkék
    'admin' => [
        'chatbot_resources' => 'Chatbot Erőforrások',
        'chatbot_resource' => 'Chatbot Erőforrás',
        'conversations' => 'Beszélgetések',
        'conversation' => 'Beszélgetés',
        'documents' => 'Dokumentumok',
        'document' => 'Dokumentum',
        'predefined_questions' => 'Előre Meghatározott Kérdések',
        'predefined_question' => 'Előre Meghatározott Kérdés',
        'messages' => 'Üzenetek',
        'message' => 'Üzenet',
        'settings' => 'Beállítások',
        'analytics' => 'Analitika',
        'statistics' => 'Statisztikák',
        
        // Form mezők
        'title' => 'Cím',
        'content' => 'Tartalom',
        'question' => 'Kérdés',
        'answer' => 'Válasz',
        'active' => 'Aktív',
        'order' => 'Sorrend',
        'rag_mode' => 'RAG Mód',
        'metadata' => 'Metaadatok',
        'tokens_used' => 'Felhasznált Tokenek',
        'role' => 'Szerep',
        'session_id' => 'Munkamenet Azonosító',
        'language' => 'Nyelv',
        'file_path' => 'Fájl Útvonal',
        'file_type' => 'Fájl Típus',
        'chunks_count' => 'Részek Száma',
        'embedding' => 'Beágyazás',
        'chunk_index' => 'Rész Index',
        'similarity_score' => 'Hasonlósági Pontszám',
        'response_time' => 'Válaszidő',
        'user_satisfaction' => 'Felhasználói Elégedettség',
        
        // Műveletek
        'enable_chatbot' => 'Chatbot Engedélyezése',
        'disable_chatbot' => 'Chatbot Letiltása',
        'view_conversations' => 'Beszélgetések Megtekintése',
        'view_messages' => 'Üzenetek Megtekintése',
        'upload_document' => 'Dokumentum Feltöltése',
        'generate_embeddings' => 'Beágyazások Generálása',
        'process_document' => 'Dokumentum Feldolgozása',
        'add_question' => 'Kérdés Hozzáadása',
        'edit_question' => 'Kérdés Szerkesztése',
        'reorder_questions' => 'Kérdések Átrendezése',
        'export_data' => 'Adatok Exportálása',
        'clear_history' => 'Történet Törlése',
        
        // Állapot
        'enabled' => 'Engedélyezett',
        'disabled' => 'Letiltott',
        'processing' => 'Feldolgozás',
        'completed' => 'Befejezett',
        'failed' => 'Sikertelen',
        'pending' => 'Függőben',
        
        // Leírások
        'chatbot_resource_description' => 'Chatbot beállítások konfigurálása ehhez az erőforráshoz.',
        'rag_mode_description' => 'Válaszd ki, hogyan generáljon válaszokat a chatbot.',
        'documents_description' => 'Dokumentumok feltöltése és kezelése a chatbot számára.',
        'predefined_questions_description' => 'Előre írt kérdések és válaszok létrehozása gyors válaszokhoz.',
        'conversation_description' => 'Felhasználók és chatbot közötti beszélgetések megtekintése és kezelése.',
        
        // Segítség szövegek
        'rag_mode_help' => 'Csak Dokumentumok: Csak feltöltött dokumentumokat használ. Dokumentumok + AI: Előnyben részesíti a dokumentumokat, de használhatja az AI tudását. Csak AI: Figyelmen kívül hagyja a dokumentumokat. Minden Dokumentum: Minden dokumentumot használ (drága).',
        'embedding_help' => 'A beágyazásokat szemantikus kereséshez használjuk a dokumentumokban.',
        'chunk_help' => 'A nagy dokumentumokat kisebb részekre bontjuk a jobb feldolgozás érdekében.',
        'temperature_help' => 'Az alacsonyabb értékek fókuszáltabb válaszokat adnak, a magasabbak kreatívabbakat.',
    ],
    
    // Validációs üzenetek
    'validation' => [
        'message_required' => 'Az üzenet megadása kötelező.',
        'message_max_length' => 'Az üzenet túl hosszú.',
        'title_required' => 'A cím megadása kötelező.',
        'content_required' => 'A tartalom megadása kötelező.',
        'question_required' => 'A kérdés megadása kötelező.',
        'answer_required' => 'A válasz megadása kötelező.',
        'file_required' => 'A fájl megadása kötelező.',
        'file_max_size' => 'A fájl túl nagy.',
        'file_invalid_type' => 'Érvénytelen fájl típus.',
    ],
    
    // Sikeres üzenetek
    'success' => [
        'chatbot_enabled' => 'A chatbot engedélyezve lett.',
        'chatbot_disabled' => 'A chatbot letiltva lett.',
        'document_uploaded' => 'A dokumentum sikeresen feltöltve.',
        'document_processed' => 'A dokumentum sikeresen feldolgozva.',
        'embeddings_generated' => 'A beágyazások sikeresen generálva.',
        'question_added' => 'A kérdés sikeresen hozzáadva.',
        'question_updated' => 'A kérdés sikeresen frissítve.',
        'question_deleted' => 'A kérdés sikeresen törölve.',
        'conversation_cleared' => 'A beszélgetés törölve.',
        'settings_updated' => 'A beállítások sikeresen frissítve.',
    ],
    
    // Hiba üzenetek
    'errors' => [
        'chatbot_not_found' => 'Chatbot erőforrás nem található.',
        'document_not_found' => 'Dokumentum nem található.',
        'conversation_not_found' => 'Beszélgetés nem található.',
        'question_not_found' => 'Kérdés nem található.',
        'file_upload_failed' => 'Fájl feltöltés sikertelen.',
        'processing_failed' => 'Feldolgozás sikertelen.',
        'embedding_generation_failed' => 'Beágyazás generálás sikertelen.',
        'ai_service_unavailable' => 'Az AI szolgáltatás jelenleg nem elérhető.',
        'rate_limit_exceeded' => 'Sebességkorlát túllépve. Várj, mielőtt újabb üzenetet küldenél.',
        'session_expired' => 'A munkamenet lejárt. Frissítsd az oldalt.',
    ],
];