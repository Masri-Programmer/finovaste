// --- Mock Data for Reviews ---

// Helper function to get a date from the past
const daysAgo = (days: number): string => {
    const date = new Date();
    date.setDate(date.getDate() - days);
    return date.toISOString();
};

// The array of mock reviews
export const mockReviews = [
    {
        id: 1,
        rating: 5,
        text: 'Tolle Investitionsmöglichkeit! Das Team ist sehr professionell und transparent mit der gesamten Dokumentation.',
        createdAt: daysAgo(2), // 2 days ago
        author: {
            name: 'Sarah Johnson',
            avatarUrl: 'https://i.pravatar.cc/150?img=1',
            initials: 'SJ',
            isVerified: true,
        },
    },
    {
        id: 2,
        rating: 4,
        text: 'Großes Potenzial für ROI. Die Immobilie befindet sich in einer erstklassigen Lage und ist gut gepflegt.',
        createdAt: daysAgo(7), // 1 week ago
        author: {
            name: 'Michael Chen',
            avatarUrl: 'https://i.pravatar.cc/150?img=2',
            initials: 'MC',
            isVerified: true,
        },
    },
    {
        id: 3,
        rating: 5,
        text: 'Reibungsloser Prozess von Anfang bis Ende. Ich freue mich darauf, meine Investition wachsen zu sehen. Sehr empfehlenswert!',
        createdAt: daysAgo(12), // 12 days ago
        author: {
            name: 'Lukas Müller',
            avatarUrl: 'https://i.pravatar.cc/150?img=3',
            initials: 'LM',
            isVerified: false, // Example of a non-verified user
        },
    },
    {
        id: 4,
        rating: 4,
        text: 'Das Dashboard ist einfach zu bedienen und die Updates sind zeitnah. Gutes Projekt.',
        createdAt: daysAgo(25), // 25 days ago
        author: {
            name: 'Emily Schmidt',
            avatarUrl: 'https://i.pravatar.cc/150?img=4',
            initials: 'ES',
            isVerified: true,
        },
    },
];

// Mock URL for the "Load More" button
export const mockNextPageUrl = '/api/reviews?page=2';
