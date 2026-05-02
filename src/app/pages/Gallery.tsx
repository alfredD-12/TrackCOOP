import { useState } from "react";
import { Search, Upload, X, Calendar, Filter, FolderPlus, Folder, Image, Trash2, Edit, Check } from "lucide-react";
import ConfirmDialog from "../components/ConfirmDialog";

type Sector = "all" | "rice_farming" | "corn" | "fishery" | "livestock" | "high_value_crops";
type ActivityType = "all" | "training_seminar" | "agricultural_intervention" | "meeting" | "community_event";

interface MediaItem {
  id: string;
  thumbnail: string;
  title: string;
  sector: Sector;
  activityType: ActivityType;
  date: string;
  description: string;
  uploadedBy: string;
  albumId?: string;
}

interface Album {
  id: string;
  name: string;
  description: string;
  coverImage: string;
  sector: Sector;
  activityType: ActivityType;
  createdDate: string;
  createdBy: string;
}

const sectorLabels: Record<Sector, string> = {
  all: "All Sectors",
  rice_farming: "Rice Farming",
  corn: "Corn",
  fishery: "Fishery",
  livestock: "Livestock",
  high_value_crops: "High-Value Crops",
};

const sectorColors: Record<Sector, string> = {
  all: "bg-blue-100 text-blue-700",
  rice_farming: "bg-green-100 text-green-700",
  corn: "bg-yellow-100 text-yellow-700",
  fishery: "bg-cyan-100 text-cyan-700",
  livestock: "bg-orange-100 text-orange-700",
  high_value_crops: "bg-purple-100 text-purple-700",
};

const activityTypeLabels: Record<ActivityType, string> = {
  all: "All Activities",
  training_seminar: "Training Seminar",
  agricultural_intervention: "Agricultural Intervention",
  meeting: "Meeting",
  community_event: "Community Event",
};

const heroImage =
  "https://images.unsplash.com/photo-1751818430558-1c2a12283155?auto=format&fit=crop&q=80&w=2400";

export default function Gallery() {
  const [viewMode, setViewMode] = useState<"albums" | "all-photos">("albums");
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedSector, setSelectedSector] = useState<Sector>("all");
  const [selectedActivityType, setSelectedActivityType] = useState<ActivityType>("all");
  const [selectedMedia, setSelectedMedia] = useState<MediaItem | null>(null);
  const [selectedAlbum, setSelectedAlbum] = useState<Album | null>(null);
  const [showUploadModal, setShowUploadModal] = useState(false);
  const [showCreateAlbumModal, setShowCreateAlbumModal] = useState(false);
  const [toastMessage, setToastMessage] = useState<string | null>(null);
  const [uploadForm, setUploadForm] = useState({
    title: "",
    sector: "rice_farming" as Sector,
    activityType: "training_seminar" as ActivityType,
    date: "",
    file: null as File | null,
    albumId: "" as string,
  });
  const [albumForm, setAlbumForm] = useState({
    name: "",
    description: "",
    sector: "rice_farming" as Sector,
    activityType: "training_seminar" as ActivityType,
  });
  const [confirmDialog, setConfirmDialog] = useState<{
    isOpen: boolean;
    title: string;
    message: string;
    onConfirm: () => void;
    variant?: "danger" | "warning" | "info" | "success";
  }>({
    isOpen: false,
    title: "",
    message: "",
    onConfirm: () => {},
  });

  const albums: Album[] = [
    {
      id: "album-1",
      name: "Rice Farming Workshop 2026",
      description: "Comprehensive training program on modern rice cultivation techniques",
      coverImage: "https://images.unsplash.com/photo-1574943320219-553eb213f72d?w=400&h=300&fit=crop",
      sector: "rice_farming",
      activityType: "training_seminar",
      createdDate: "Mar 15, 2026",
      createdBy: "Maria Santos"
    },
    {
      id: "album-2",
      name: "Corn Harvest Festival 2026",
      description: "Annual celebration with cooperative members and families",
      coverImage: "https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=400&h=300&fit=crop",
      sector: "corn",
      activityType: "community_event",
      createdDate: "Mar 20, 2026",
      createdBy: "Juan Dela Cruz"
    },
    {
      id: "album-3",
      name: "Fishery Infrastructure Upgrade",
      description: "Documentation of new aquaculture equipment installation",
      coverImage: "https://images.unsplash.com/photo-1535231540604-72e8fbaf8cdb?w=400&h=300&fit=crop",
      sector: "fishery",
      activityType: "agricultural_intervention",
      createdDate: "Apr 1, 2026",
      createdBy: "Rosa Garcia"
    },
    {
      id: "album-4",
      name: "Livestock Management Training",
      description: "Expert-led sessions on animal health and nutrition",
      coverImage: "https://images.unsplash.com/photo-1560493676-04071c5f467b?w=400&h=300&fit=crop",
      sector: "livestock",
      activityType: "training_seminar",
      createdDate: "Apr 5, 2026",
      createdBy: "Pedro Reyes"
    },
  ];

  // Helper function to get photo count for an album
  const getAlbumPhotoCount = (albumId: string): number => {
    return mediaItems.filter(item => item.albumId === albumId).length;
  };

  const mediaItems: MediaItem[] = [
    // Album 1: Rice Farming Workshop 2026 (5 photos)
    {
      id: "media-1",
      thumbnail: "https://images.unsplash.com/photo-1574943320219-553eb213f72d?w=400&h=300&fit=crop",
      title: "Rice Planting Training Workshop",
      sector: "rice_farming",
      activityType: "training_seminar",
      date: "Mar 15, 2026",
      description: "Community members learning modern rice planting techniques and best practices for sustainable farming.",
      uploadedBy: "Maria Santos",
      albumId: "album-1"
    },
    {
      id: "media-2",
      thumbnail: "https://images.unsplash.com/photo-1593113598332-cd288d649433?w=400&h=300&fit=crop",
      title: "Organic Fertilizer Distribution",
      sector: "rice_farming",
      activityType: "training_seminar",
      date: "Mar 15, 2026",
      description: "Distribution of organic fertilizer to rice farming members as part of sustainability program.",
      uploadedBy: "Maria Santos",
      albumId: "album-1"
    },
    {
      id: "media-3",
      thumbnail: "https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=400&h=300&fit=crop",
      title: "Field Demonstration",
      sector: "rice_farming",
      activityType: "training_seminar",
      date: "Mar 15, 2026",
      description: "Hands-on field demonstration of proper rice seedling transplantation techniques.",
      uploadedBy: "Maria Santos",
      albumId: "album-1"
    },
    {
      id: "media-4",
      thumbnail: "https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=400&h=300&fit=crop",
      title: "Workshop Participants",
      sector: "rice_farming",
      activityType: "training_seminar",
      date: "Mar 15, 2026",
      description: "Group photo of all workshop participants and trainers.",
      uploadedBy: "Maria Santos",
      albumId: "album-1"
    },
    {
      id: "media-5",
      thumbnail: "https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=400&h=300&fit=crop",
      title: "Certificate Distribution",
      sector: "rice_farming",
      activityType: "training_seminar",
      date: "Mar 15, 2026",
      description: "Distribution of certificates of completion to all workshop attendees.",
      uploadedBy: "Maria Santos",
      albumId: "album-1"
    },

    // Album 2: Corn Harvest Festival 2026 (5 photos)
    {
      id: "media-6",
      thumbnail: "https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=400&h=300&fit=crop",
      title: "Corn Harvest Festival Opening",
      sector: "corn",
      activityType: "community_event",
      date: "Mar 20, 2026",
      description: "Annual corn harvest celebration with cooperative members and their families.",
      uploadedBy: "Juan Dela Cruz",
      albumId: "album-2"
    },
    {
      id: "media-7",
      thumbnail: "https://images.unsplash.com/photo-1574943320219-553eb213f72d?w=400&h=300&fit=crop",
      title: "Traditional Corn Recipes",
      sector: "corn",
      activityType: "community_event",
      date: "Mar 20, 2026",
      description: "Cooking demonstration featuring traditional corn-based dishes.",
      uploadedBy: "Juan Dela Cruz",
      albumId: "album-2"
    },
    {
      id: "media-8",
      thumbnail: "https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=400&h=300&fit=crop",
      title: "Children's Activities",
      sector: "corn",
      activityType: "community_event",
      date: "Mar 20, 2026",
      description: "Fun activities and games for children at the harvest festival.",
      uploadedBy: "Juan Dela Cruz",
      albumId: "album-2"
    },
    {
      id: "media-9",
      thumbnail: "https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=400&h=300&fit=crop",
      title: "Corn Product Exhibition",
      sector: "corn",
      activityType: "community_event",
      date: "Mar 20, 2026",
      description: "Display of various corn products produced by cooperative members.",
      uploadedBy: "Juan Dela Cruz",
      albumId: "album-2"
    },
    {
      id: "media-10",
      thumbnail: "https://images.unsplash.com/photo-1593113598332-cd288d649433?w=400&h=300&fit=crop",
      title: "Festival Closing Ceremony",
      sector: "corn",
      activityType: "community_event",
      date: "Mar 20, 2026",
      description: "Closing ceremony with awards for best corn producers.",
      uploadedBy: "Juan Dela Cruz",
      albumId: "album-2"
    },

    // Album 3: Fishery Infrastructure Upgrade (5 photos)
    {
      id: "media-11",
      thumbnail: "https://images.unsplash.com/photo-1535231540604-72e8fbaf8cdb?w=400&h=300&fit=crop",
      title: "Fish Farm Infrastructure Upgrade",
      sector: "fishery",
      activityType: "agricultural_intervention",
      date: "Apr 1, 2026",
      description: "Installation of new aquaculture equipment and improved water circulation systems.",
      uploadedBy: "Rosa Garcia",
      albumId: "album-3"
    },
    {
      id: "media-12",
      thumbnail: "https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400&h=300&fit=crop",
      title: "Water Filtration System",
      sector: "fishery",
      activityType: "agricultural_intervention",
      date: "Apr 1, 2026",
      description: "New advanced water filtration and purification system installation.",
      uploadedBy: "Rosa Garcia",
      albumId: "album-3"
    },
    {
      id: "media-13",
      thumbnail: "https://images.unsplash.com/photo-1490730141103-6cac27aaab94?w=400&h=300&fit=crop",
      title: "Pond Aeration Equipment",
      sector: "fishery",
      activityType: "agricultural_intervention",
      date: "Apr 1, 2026",
      description: "Installation of modern pond aeration systems to improve fish health.",
      uploadedBy: "Rosa Garcia",
      albumId: "album-3"
    },
    {
      id: "media-14",
      thumbnail: "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=300&fit=crop",
      title: "Feed Storage Facility",
      sector: "fishery",
      activityType: "agricultural_intervention",
      date: "Apr 1, 2026",
      description: "New climate-controlled feed storage facility construction.",
      uploadedBy: "Rosa Garcia",
      albumId: "album-3"
    },
    {
      id: "media-15",
      thumbnail: "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=400&h=300&fit=crop",
      title: "Project Completion",
      sector: "fishery",
      activityType: "agricultural_intervention",
      date: "Apr 1, 2026",
      description: "Final inspection and handover of completed infrastructure upgrades.",
      uploadedBy: "Rosa Garcia",
      albumId: "album-3"
    },

    // Album 4: Livestock Management Training (5 photos)
    {
      id: "media-16",
      thumbnail: "https://images.unsplash.com/photo-1560493676-04071c5f467b?w=400&h=300&fit=crop",
      title: "Livestock Management Seminar",
      sector: "livestock",
      activityType: "training_seminar",
      date: "Apr 5, 2026",
      description: "Expert-led training on animal health, nutrition, and modern livestock management practices.",
      uploadedBy: "Pedro Reyes",
      albumId: "album-4"
    },
    {
      id: "media-17",
      thumbnail: "https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=400&h=300&fit=crop",
      title: "Animal Nutrition Workshop",
      sector: "livestock",
      activityType: "training_seminar",
      date: "Apr 5, 2026",
      description: "Detailed session on proper animal nutrition and feeding schedules.",
      uploadedBy: "Pedro Reyes",
      albumId: "album-4"
    },
    {
      id: "media-18",
      thumbnail: "https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=400&h=300&fit=crop",
      title: "Veterinary Care Demonstration",
      sector: "livestock",
      activityType: "training_seminar",
      date: "Apr 5, 2026",
      description: "Hands-on demonstration of basic veterinary care and disease prevention.",
      uploadedBy: "Pedro Reyes",
      albumId: "album-4"
    },
    {
      id: "media-19",
      thumbnail: "https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=400&h=300&fit=crop",
      title: "Farm Visit",
      sector: "livestock",
      activityType: "training_seminar",
      date: "Apr 5, 2026",
      description: "Field visit to a model livestock farm showcasing best practices.",
      uploadedBy: "Pedro Reyes",
      albumId: "album-4"
    },
    {
      id: "media-20",
      thumbnail: "https://images.unsplash.com/photo-1574943320219-553eb213f72d?w=400&h=300&fit=crop",
      title: "Training Completion",
      sector: "livestock",
      activityType: "training_seminar",
      date: "Apr 5, 2026",
      description: "Group photo and certificate distribution ceremony.",
      uploadedBy: "Pedro Reyes",
      albumId: "album-4"
    },
  ];

  const filteredAlbums = albums.filter(album => {
    const matchesSearch = album.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         album.description.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesSector = selectedSector === "all" || album.sector === selectedSector;
    const matchesActivity = selectedActivityType === "all" || album.activityType === selectedActivityType;
    return matchesSearch && matchesSector && matchesActivity;
  });

  const filteredMedia = mediaItems.filter(item => {
    const matchesSearch = item.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         item.description.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesSector = selectedSector === "all" || item.sector === selectedSector;
    const matchesActivity = selectedActivityType === "all" || item.activityType === selectedActivityType;

    // If viewing an album, only show photos from that album
    if (selectedAlbum) {
      return item.albumId === selectedAlbum.id && matchesSearch && matchesSector && matchesActivity;
    }

    // In "all-photos" mode, show all photos
    return matchesSearch && matchesSector && matchesActivity;
  });

  const handleUploadSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setConfirmDialog({
      isOpen: true,
      title: "Upload Media?",
      message: "Are you sure you want to upload this media file? It will be added to the gallery and visible to all members.",
      variant: "success",
      onConfirm: () => {
        setShowUploadModal(false);
        setUploadForm({
          title: "",
          sector: "rice_farming",
          activityType: "training_seminar",
          date: "",
          file: null,
          albumId: "",
        });
        setToastMessage("Media uploaded successfully!");
        setTimeout(() => setToastMessage(null), 3000);
      },
    });
  };

  const handleCreateAlbumSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setConfirmDialog({
      isOpen: true,
      title: "Create Album?",
      message: "Are you sure you want to create this album? You can add photos to it after creation.",
      variant: "success",
      onConfirm: () => {
        setShowCreateAlbumModal(false);
        setAlbumForm({
          name: "",
          description: "",
          sector: "rice_farming",
          activityType: "training_seminar",
        });
        setToastMessage("Album created successfully!");
        setTimeout(() => setToastMessage(null), 3000);
      },
    });
  };

  const handleDeletePhoto = (photo: MediaItem) => {
    setConfirmDialog({
      isOpen: true,
      title: "Delete Photo?",
      message: `Are you sure you want to delete "${photo.title}"? This action cannot be undone.`,
      variant: "danger",
      onConfirm: () => {
        console.log("Photo deleted:", photo.id);
        setSelectedMedia(null);
      },
    });
  };

  const handleDeleteAlbum = (album: Album, e: React.MouseEvent) => {
    e.stopPropagation();
    setConfirmDialog({
      isOpen: true,
      title: "Delete Album?",
      message: `Are you sure you want to delete "${album.name}"? All photos in this album will remain in the gallery but will no longer be part of this album. This action cannot be undone.`,
      variant: "danger",
      onConfirm: () => {
        console.log("Album deleted:", album.id);
      },
    });
  };

  return (
    <div className="min-h-full bg-stone-50 text-gray-950">
      <section className="relative overflow-hidden border-b border-stone-200">
        <img
          src={heroImage}
          alt=""
          aria-hidden="true"
          className="absolute inset-0 h-full w-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/15" />
        <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-stone-50 to-transparent" />

        <div className="relative mx-auto flex min-h-[280px] max-w-[1600px] flex-col justify-start px-6 py-8 md:min-h-[320px] md:px-8 md:py-10">
          <div className="animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div className="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
              <div className="max-w-4xl">
                <p className="mb-4 inline-flex rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-sm backdrop-blur">
                  Gallery
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  Media Repository
                </h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">
                  {selectedAlbum ? `Album: ${selectedAlbum.name}` : "Media archive of cooperative activities and events"}
                </p>
              </div>

              <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center lg:justify-end">
                {selectedAlbum && (
                  <button
                    onClick={() => setSelectedAlbum(null)}
                    className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-white/10 px-5 py-3 font-semibold text-white shadow-sm backdrop-blur transition-all hover:-translate-y-1 hover:bg-white/20 border border-white/20"
                  >
                    <X className="h-4 w-4" />
                    Close Album
                  </button>
                )}
                {!selectedAlbum && viewMode === "albums" && (
                  <button
                    onClick={() => setShowCreateAlbumModal(true)}
                    data-tour="gallery-create-album"
                    className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-white px-5 py-3 font-semibold text-gray-900 shadow-lg transition-all hover:-translate-y-1 hover:bg-gray-50"
                  >
                    <FolderPlus className="h-4 w-4" />
                    Create Album
                  </button>
                )}
                <button
                  onClick={() => setShowUploadModal(true)}
                  data-tour="gallery-upload-media"
                  className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200"
                >
                  <Upload className="h-4 w-4" />
                  Upload Media
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        {/* View Mode Tabs */}
      {!selectedAlbum && (
        <div className="mb-6 border-b border-border">
          <div className="flex gap-1">
            <button
              onClick={() => setViewMode("albums")}
              className={`px-6 py-3 font-medium transition-all relative ${
                viewMode === "albums"
                  ? "text-primary"
                  : "text-muted-foreground hover:text-foreground"
              }`}
            >
              <div className="flex items-center gap-2">
                <Folder className="w-5 h-5" />
                <span>Albums</span>
              </div>
              {viewMode === "albums" && (
                <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"></div>
              )}
            </button>
            <button
              onClick={() => setViewMode("all-photos")}
              className={`px-6 py-3 font-medium transition-all relative ${
                viewMode === "all-photos"
                  ? "text-primary"
                  : "text-muted-foreground hover:text-foreground"
              }`}
            >
              <div className="flex items-center gap-2">
                <Image className="w-5 h-5" />
                <span>All Photos</span>
              </div>
              {viewMode === "all-photos" && (
                <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"></div>
              )}
            </button>
          </div>
        </div>
      )}

      {/* Filters */}
      <div
        className="bg-card rounded-xl p-6 border border-border shadow-sm mb-8"
        data-tour="gallery-filters"
      >
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          {/* Search */}
          <div className="md:col-span-2">
            <label className="block text-sm font-medium mb-2">Search</label>
            <div className="relative">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search by title or description..."
                className="w-full pl-10 pr-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
              />
            </div>
          </div>

          {/* Sector Filter */}
          <div>
            <label className="block text-sm font-medium mb-2">Sector</label>
            <select
              value={selectedSector}
              onChange={(e) => setSelectedSector(e.target.value as Sector)}
              className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
            >
              {(Object.keys(sectorLabels) as Sector[]).map(sector => (
                <option key={sector} value={sector}>{sectorLabels[sector]}</option>
              ))}
            </select>
          </div>

          {/* Activity Type Filter */}
          <div>
            <label className="block text-sm font-medium mb-2">Activity Type</label>
            <select
              value={selectedActivityType}
              onChange={(e) => setSelectedActivityType(e.target.value as ActivityType)}
              className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
            >
              {(Object.keys(activityTypeLabels) as ActivityType[]).map(type => (
                <option key={type} value={type}>{activityTypeLabels[type]}</option>
              ))}
            </select>
          </div>
        </div>

        {/* Active Filters Info */}
        <div className="flex items-center gap-2 mt-4 text-sm text-muted-foreground">
          <Filter className="w-4 h-4" />
          <span>
            Showing {viewMode === "albums" && !selectedAlbum ? filteredAlbums.length : filteredMedia.length} of{" "}
            {viewMode === "albums" && !selectedAlbum ? albums.length : mediaItems.length} items
          </span>
        </div>
      </div>

      {/* Albums Grid */}
      {viewMode === "albums" && !selectedAlbum && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredAlbums.map((album, index) => (
            <div
              key={album.id}
              onClick={() => setSelectedAlbum(album)}
              className="bg-card rounded-xl border border-border shadow-sm overflow-hidden cursor-pointer transition-all duration-300 animate-in fade-in slide-in-from-bottom-3 hover:-translate-y-1 hover:shadow-lg group"
              style={{ animationDelay: `${Math.min(index * 50, 300)}ms` }}
            >
              <div className="relative aspect-[4/3] overflow-hidden bg-muted">
                <img
                  src={album.coverImage}
                  alt={album.name}
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div className="absolute top-3 right-3 bg-black/70 text-white px-3 py-1 rounded-lg flex items-center gap-2 text-sm">
                  <Image className="w-4 h-4" />
                  <span>{getAlbumPhotoCount(album.id)}</span>
                </div>
                <div className="absolute top-3 left-3 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                  <button
                    onClick={(e) => handleDeleteAlbum(album, e)}
                    className="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors shadow-lg"
                    title="Delete Album"
                  >
                    <Trash2 className="w-4 h-4" />
                  </button>
                </div>
              </div>
              <div className="p-4">
                <h3 className="font-bold mb-2 group-hover:text-primary transition-colors">{album.name}</h3>
                <p className="text-sm text-muted-foreground mb-3 line-clamp-2">{album.description}</p>
                <div className="flex items-center justify-between">
                  <span className={`px-3 py-1 rounded-full text-xs ${sectorColors[album.sector]}`}>
                    {sectorLabels[album.sector]}
                  </span>
                  <div className="flex items-center gap-1 text-xs text-muted-foreground">
                    <Calendar className="w-3 h-3" />
                    <span>{album.createdDate}</span>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Media Grid */}
      {(viewMode === "all-photos" || selectedAlbum) && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredMedia.map((item, index) => (
          <div
            key={item.id}
            onClick={() => setSelectedMedia(item)}
            className="bg-card rounded-xl border border-border shadow-sm overflow-hidden cursor-pointer transition-all duration-300 animate-in fade-in slide-in-from-bottom-3 hover:-translate-y-1 hover:shadow-lg group"
            style={{ animationDelay: `${Math.min(index * 50, 300)}ms` }}
          >
            <div className="relative aspect-[4/3] overflow-hidden bg-muted">
              <img
                src={item.thumbnail}
                alt={item.title}
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div className="p-4">
              <h3 className="font-bold mb-2 group-hover:text-primary transition-colors">{item.title}</h3>
              <div className="flex items-center justify-between">
                <span className={`px-3 py-1 rounded-full text-xs ${sectorColors[item.sector]}`}>
                  {sectorLabels[item.sector]}
                </span>
                <div className="flex items-center gap-1 text-xs text-muted-foreground">
                  <Calendar className="w-3 h-3" />
                  <span>{item.date}</span>
                </div>
              </div>
            </div>
          </div>
        ))}
        </div>
      )}

      {/* Empty State - Albums */}
      {viewMode === "albums" && !selectedAlbum && filteredAlbums.length === 0 && (
        <div className="bg-card rounded-xl border border-border shadow-sm p-12 text-center">
          <Folder className="w-12 h-12 text-muted-foreground mx-auto mb-4" />
          <h3 className="font-bold mb-2">No albums found</h3>
          <p className="text-sm text-muted-foreground mb-4">Try adjusting your search or filters</p>
          <button
            onClick={() => setShowCreateAlbumModal(true)}
            className="px-6 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all inline-flex items-center gap-2"
          >
            <FolderPlus className="w-4 h-4" />
            Create Your First Album
          </button>
        </div>
      )}

      {/* Empty State - Photos */}
      {(viewMode === "all-photos" || selectedAlbum) && filteredMedia.length === 0 && (
        <div className="bg-card rounded-xl border border-border shadow-sm p-12 text-center">
          <Search className="w-12 h-12 text-muted-foreground mx-auto mb-4" />
          <h3 className="font-bold mb-2">No media found</h3>
          <p className="text-sm text-muted-foreground">Try adjusting your search or filters</p>
        </div>
      )}

      {/* Lightbox Modal */}
      {selectedMedia && (
        <div className="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4" onClick={() => setSelectedMedia(null)}>
          <div
            className="bg-background rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-in fade-in zoom-in-95 duration-200"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Modal Header */}
            <div className="sticky top-0 bg-primary text-primary-foreground p-6 rounded-t-xl flex items-center justify-between z-10">
              <h2 className="text-2xl font-display">{selectedMedia.title}</h2>
              <button
                onClick={() => setSelectedMedia(null)}
                className="p-2 hover:bg-white/20 rounded-lg transition-colors"
              >
                <X className="w-6 h-6" />
              </button>
            </div>

            {/* Modal Content */}
            <div className="p-6">
              {/* Full Image */}
              <div className="relative aspect-video rounded-xl overflow-hidden mb-6 bg-muted">
                <img
                  src={selectedMedia.thumbnail}
                  alt={selectedMedia.title}
                  className="w-full h-full object-cover"
                />
              </div>

              {/* Details */}
              <div className="space-y-4">
                <div>
                  <h3 className="text-sm font-medium text-muted-foreground mb-2">Description</h3>
                  <p className="text-foreground leading-relaxed">{selectedMedia.description}</p>
                </div>

                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div>
                    <h3 className="text-sm font-medium text-muted-foreground mb-2">Sector</h3>
                    <span className={`inline-block px-3 py-1 rounded-full text-sm ${sectorColors[selectedMedia.sector]}`}>
                      {sectorLabels[selectedMedia.sector]}
                    </span>
                  </div>

                  <div>
                    <h3 className="text-sm font-medium text-muted-foreground mb-2">Activity Type</h3>
                    <p className="text-sm">{activityTypeLabels[selectedMedia.activityType]}</p>
                  </div>

                  <div>
                    <h3 className="text-sm font-medium text-muted-foreground mb-2">Date Taken</h3>
                    <div className="flex items-center gap-1 text-sm">
                      <Calendar className="w-4 h-4 text-muted-foreground" />
                      <span>{selectedMedia.date}</span>
                    </div>
                  </div>

                  <div>
                    <h3 className="text-sm font-medium text-muted-foreground mb-2">Uploaded By</h3>
                    <p className="text-sm">{selectedMedia.uploadedBy}</p>
                  </div>
                </div>

                {/* Action Buttons */}
                <div className="flex gap-3 pt-4 border-t border-border">
                  <button
                    onClick={() => handleDeletePhoto(selectedMedia)}
                    className="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all flex items-center gap-2"
                  >
                    <Trash2 className="w-4 h-4" />
                    Delete Photo
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Upload Media Modal */}
      {showUploadModal && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onClick={() => setShowUploadModal(false)}>
          <div
            className="bg-background rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-in fade-in zoom-in-95 duration-200"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Modal Header */}
            <div className="sticky top-0 bg-primary text-primary-foreground p-6 rounded-t-xl flex items-center justify-between">
              <h2 className="text-2xl font-display">Upload Media</h2>
              <button
                onClick={() => setShowUploadModal(false)}
                className="p-2 hover:bg-white/20 rounded-lg transition-colors"
              >
                <X className="w-6 h-6" />
              </button>
            </div>

            {/* Modal Content */}
            <div className="p-6">
              <form onSubmit={handleUploadSubmit} className="space-y-5">
                {/* File Upload */}
                <div>
                  <label className="block text-sm font-medium mb-2">Photo/Video File</label>
                  <div className="border-2 border-dashed border-border rounded-xl p-8 text-center hover:border-primary transition-colors cursor-pointer">
                    <Upload className="w-8 h-8 text-muted-foreground mx-auto mb-2" />
                    <p className="text-sm text-muted-foreground mb-1">Click to upload or drag and drop</p>
                    <p className="text-xs text-muted-foreground">PNG, JPG, MP4 up to 10MB</p>
                    <input
                      type="file"
                      accept="image/*,video/*"
                      onChange={(e) => setUploadForm({ ...uploadForm, file: e.target.files?.[0] || null })}
                      className="hidden"
                    />
                  </div>
                </div>

                {/* Activity Title */}
                <div>
                  <label className="block text-sm font-medium mb-2">Activity Title</label>
                  <input
                    type="text"
                    value={uploadForm.title}
                    onChange={(e) => setUploadForm({ ...uploadForm, title: e.target.value })}
                    className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    placeholder="Enter activity title"
                    required
                  />
                </div>

                {/* Album Selection */}
                <div>
                  <label className="block text-sm font-medium mb-2">Add to Album (Optional)</label>
                  <select
                    value={uploadForm.albumId}
                    onChange={(e) => setUploadForm({ ...uploadForm, albumId: e.target.value })}
                    className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                  >
                    <option value="">No Album (Standalone Photo)</option>
                    {albums.map(album => (
                      <option key={album.id} value={album.id}>{album.name}</option>
                    ))}
                  </select>
                  <p className="text-xs text-muted-foreground mt-2">
                    You can add this photo to an existing album or keep it as a standalone photo
                  </p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {/* Sector */}
                  <div>
                    <label className="block text-sm font-medium mb-2">Sector</label>
                    <select
                      value={uploadForm.sector}
                      onChange={(e) => setUploadForm({ ...uploadForm, sector: e.target.value as Sector })}
                      className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                      {(Object.keys(sectorLabels) as Sector[])
                        .filter(s => s !== "all")
                        .map(sector => (
                          <option key={sector} value={sector}>{sectorLabels[sector]}</option>
                        ))}
                    </select>
                  </div>

                  {/* Activity Type */}
                  <div>
                    <label className="block text-sm font-medium mb-2">Activity Type</label>
                    <select
                      value={uploadForm.activityType}
                      onChange={(e) => setUploadForm({ ...uploadForm, activityType: e.target.value as ActivityType })}
                      className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                      {(Object.keys(activityTypeLabels) as ActivityType[])
                        .filter(t => t !== "all")
                        .map(type => (
                          <option key={type} value={type}>{activityTypeLabels[type]}</option>
                        ))}
                    </select>
                  </div>
                </div>

                {/* Date */}
                <div>
                  <label className="block text-sm font-medium mb-2">Date Taken</label>
                  <input
                    type="date"
                    value={uploadForm.date}
                    onChange={(e) => setUploadForm({ ...uploadForm, date: e.target.value })}
                    className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    required
                  />
                </div>

                <div className="flex gap-3 pt-4">
                  <button
                    type="button"
                    onClick={() => setShowUploadModal(false)}
                    className="flex-1 px-6 py-3 border border-border rounded-lg hover:bg-muted transition-all"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="flex-1 px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all flex items-center justify-center gap-2"
                  >
                    <Upload className="w-4 h-4" />
                    Upload Media
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* Create Album Modal */}
      {showCreateAlbumModal && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onClick={() => setShowCreateAlbumModal(false)}>
          <div
            className="bg-background rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-in fade-in zoom-in-95 duration-200"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Modal Header */}
            <div className="sticky top-0 bg-primary text-primary-foreground p-6 rounded-t-xl flex items-center justify-between">
              <h2 className="text-2xl font-display">Create New Album</h2>
              <button
                onClick={() => setShowCreateAlbumModal(false)}
                className="p-2 hover:bg-white/20 rounded-lg transition-colors"
              >
                <X className="w-6 h-6" />
              </button>
            </div>

            {/* Modal Content */}
            <div className="p-6">
              <form onSubmit={handleCreateAlbumSubmit} className="space-y-5">
                {/* Album Name */}
                <div>
                  <label className="block text-sm font-medium mb-2">Album Name</label>
                  <input
                    type="text"
                    value={albumForm.name}
                    onChange={(e) => setAlbumForm({ ...albumForm, name: e.target.value })}
                    className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    placeholder="Enter album name"
                    required
                  />
                </div>

                {/* Album Description */}
                <div>
                  <label className="block text-sm font-medium mb-2">Description</label>
                  <textarea
                    value={albumForm.description}
                    onChange={(e) => setAlbumForm({ ...albumForm, description: e.target.value })}
                    className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring min-h-[100px]"
                    placeholder="Describe what this album is about..."
                    required
                  />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {/* Sector */}
                  <div>
                    <label className="block text-sm font-medium mb-2">Sector</label>
                    <select
                      value={albumForm.sector}
                      onChange={(e) => setAlbumForm({ ...albumForm, sector: e.target.value as Sector })}
                      className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                      {(Object.keys(sectorLabels) as Sector[])
                        .filter(s => s !== "all")
                        .map(sector => (
                          <option key={sector} value={sector}>{sectorLabels[sector]}</option>
                        ))}
                    </select>
                  </div>

                  {/* Activity Type */}
                  <div>
                    <label className="block text-sm font-medium mb-2">Activity Type</label>
                    <select
                      value={albumForm.activityType}
                      onChange={(e) => setAlbumForm({ ...albumForm, activityType: e.target.value as ActivityType })}
                      className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                      {(Object.keys(activityTypeLabels) as ActivityType[])
                        .filter(t => t !== "all")
                        .map(type => (
                          <option key={type} value={type}>{activityTypeLabels[type]}</option>
                        ))}
                    </select>
                  </div>
                </div>

                <div className="flex gap-3 pt-4">
                  <button
                    type="button"
                    onClick={() => setShowCreateAlbumModal(false)}
                    className="flex-1 px-6 py-3 border border-border rounded-lg hover:bg-muted transition-all"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="flex-1 px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all flex items-center justify-center gap-2"
                  >
                    <FolderPlus className="w-4 h-4" />
                    Create Album
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* Confirmation Dialog */}
      <ConfirmDialog
        isOpen={confirmDialog.isOpen}
        onClose={() => setConfirmDialog({ ...confirmDialog, isOpen: false })}
        onConfirm={confirmDialog.onConfirm}
        title={confirmDialog.title}
        message={confirmDialog.message}
        variant={confirmDialog.variant}
      />
      {/* Toast Notification */}
      {toastMessage && (
        <div className="fixed bottom-6 right-6 bg-white border border-green-200 text-green-950 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-in slide-in-from-bottom-5 fade-in duration-300 z-50">
          <div className="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
            <Check className="w-5 h-5 text-green-600" />
          </div>
          <span className="font-medium">{toastMessage}</span>
        </div>
      )}
      </main>
    </div>
  );
}
