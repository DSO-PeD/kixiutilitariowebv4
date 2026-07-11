export const formatarDataHora = (data) => {
    if (!data) return ''
    
    const d = new Date(data)
    
    const dia = String(d.getDate()).padStart(2, '0')
    const mes = String(d.getMonth() + 1).padStart(2, '0')
    const ano = d.getFullYear()
    const hora = String(d.getHours()).padStart(2, '0')
    const minutos = String(d.getMinutes()).padStart(2, '0')

    return `${dia}/${mes}/${ano} ${hora}:${minutos}`
}

export const primeiroUltimoNome = (nomeCompleto) => {
    if (!nomeCompleto) return ''
    
    const partes = nomeCompleto.trim().split(' ').filter(p => p !== '')
    
    if (partes.length === 1) return partes[0]
    
    return `${partes[0]} ${partes[partes.length - 1]}`
}