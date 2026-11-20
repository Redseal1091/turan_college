/**
 * Maps a specialty to its corresponding 3D illustration card image from turan_college__media
 */
export const getSpecialtyImage = (specialty: { slug?: string; title?: string; code?: string }): string => {
    const slug = (specialty.slug || '').toLowerCase();
    const title = (specialty.title || '').toLowerCase();
    const code = specialty.code || '';

    if (slug.includes('market') || title.includes('маркетинг') || code === '04140100') {
        return '/images/cards/marketing.png';
    }
    if (slug.includes('tour') || title.includes('туризм') || code === '10150100') {
        return '/images/cards/turism.png';
    }
    if (
        slug.includes('soft') ||
        slug.includes('dev') ||
        title.includes('программ') ||
        title.includes('информацион') ||
        code === '06130100'
    ) {
        return '/images/cards/po.png';
    }
    if (
        slug.includes('juris') ||
        slug.includes('law') ||
        title.includes('право') ||
        title.includes('юрист') ||
        code === '04210100'
    ) {
        return '/images/cards/pravo.png';
    }
    if (slug.includes('trans') || title.includes('перевод') || code === '02310100') {
        return '/images/cards/perevod.png';
    }
    if (
        slug.includes('audit') ||
        slug.includes('account') ||
        title.includes('учет') ||
        title.includes('аудит') ||
        code === '04110100'
    ) {
        return '/images/cards/audit.png';
    }
    if (slug.includes('manag') || title.includes('менеджмент') || code === '04130100') {
        return '/images/cards/menedjment.png';
    }
    if (
        slug.includes('bank') ||
        slug.includes('insur') ||
        title.includes('банк') ||
        title.includes('страхов') ||
        code === '04120100'
    ) {
        return '/images/cards/bank.png';
    }

    return '/images/cards/po.png';
};

