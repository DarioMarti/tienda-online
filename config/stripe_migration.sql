-- Script SQL para añadir campos de Stripe a la tabla pedidos
-- Ejecuta esto solo si los campos no existen

-- Añadir columnas para Stripe (si no existen)
ALTER TABLE pedidos 
ADD COLUMN IF NOT EXISTS stripe_session_id VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS stripe_payment_intent_id VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS metodo_pago VARCHAR(50) DEFAULT 'card';

-- Crear índice para búsquedas rápidas
CREATE INDEX IF NOT EXISTS idx_stripe_session ON pedidos(stripe_session_id);
CREATE INDEX IF NOT EXISTS idx_stripe_payment_intent ON pedidos(stripe_payment_intent_id);
