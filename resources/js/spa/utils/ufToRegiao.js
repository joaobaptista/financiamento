// Mapeamento UF -> Região do Brasil
export const ufToRegiao = {
    // Norte
    'AC': 'norte',
    'AP': 'norte',
    'AM': 'norte',
    'PA': 'norte',
    'RO': 'norte',
    'RR': 'norte',
    'TO': 'norte',
    
    // Nordeste
    'AL': 'nordeste',
    'BA': 'nordeste',
    'CE': 'nordeste',
    'MA': 'nordeste',
    'PB': 'nordeste',
    'PE': 'nordeste',
    'PI': 'nordeste',
    'RN': 'nordeste',
    'SE': 'nordeste',
    
    // Centro-Oeste
    'DF': 'centro-oeste',
    'GO': 'centro-oeste',
    'MT': 'centro-oeste',
    'MS': 'centro-oeste',
    
    // Sudeste
    'ES': 'sudeste',
    'MG': 'sudeste',
    'RJ': 'sudeste',
    'SP': 'sudeste',
    
    // Sul
    'PR': 'sul',
    'RS': 'sul',
    'SC': 'sul',
};

export function getRegiaoFromUF(uf) {
    if (!uf) return null;
    const ufUpper = String(uf).toUpperCase().trim();
    return ufToRegiao[ufUpper] || null;
}
